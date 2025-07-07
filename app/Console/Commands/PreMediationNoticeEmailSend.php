<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\FileCase;
use App\Models\MediationNotice;
use App\Models\NoticeTemplate;
use App\Models\Organization;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PreMediationNoticeEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:premediation-notice-email-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // #####################################################
        // Pre-Mediation Notice Send Via Email - By Case Manager
        // #####################################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->leftJoin('mediation_notices', 'mediation_notices.file_case_id', '=', 'file_cases.id')
            ->where('mediation_notices.mediation_notice_type', 1)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_email')
                    ->where('file_cases.respondent_email', '!=', '');
            })
            ->where(function ($query) {
                $query->where('mediation_notices.email_status', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNull('mediation_notices.notice_copy')
                                ->where('mediation_notices.email_status', 0);
                    });
            })
            ->select(
                'file_cases.*',
                'mediation_notices.file_case_id',
                'mediation_notices.mediation_notice_type',
                'mediation_notices.notice_copy',
                'mediation_notices.email_status'
            )
            ->limit(3)
            ->get();
        
        foreach ($caseData as $key => $value) {
            try {

                $noticetemplateData = NoticeTemplate::where('id', 11)->first();
                $noticeTemplate     = $noticetemplateData->notice_format;

                $organizationManager_signature = Organization::where('id', $value['organization_id'])->select('signature_org')->first();
                $organizationLetterHead_header = Organization::where('id', $value['organization_id'])->select('header_letterhead')->first();
                $organizationLetterHead_footer = Organization::where('id', $value['organization_id'])->select('footer_letterhead')->first();
                $now                           = now();
                
                $fileCaseId = $value->id;
                Log::info("Processing Mediation Email for FileCase ID: {$fileCaseId}");

                // #########################################################
                // ################# Send Email using SMTP #################
                if (empty($value->notice_copy)) {
                    // Define your replacement values
                    $data = [
                        'BANK/ORGANISATION/CLAIMANT NAME'               => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                        'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                        'CUSTOMER NAME'                                 => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                        'CUSTOMER ADDRESS'                              => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                        'CUSTOMER MOBILE NO'                            => $value->respondent_mobile ?? '',
                        'CUSTOMER MAIL ID'                              => $value->respondent_email ?? '',

                        'GUARANTOR ADDRESS'                             => $value->guarantors->guarantor_1_address ?? '',
                        'GUARANTOR MOBILE NO'                           => $value->guarantors->guarantor_1_mobile_no ?? '',
                        'GUARANTOR MAIL ID'                             => $value->guarantors->guarantor_1_email_id ?? '',

                        'CLAIM SIGNATORY/AUTHORISED OFFICER NAME'       => $value->file_case_details->claim_signatory_authorised_officer_name ?? '',
                        'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'  => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',

                        'CASE REGISTRATION NUMBER'                      => $value->case_number ?? '',
                        'LOAN NO'                                       => $value->loan_number ?? '',
                        'FORECLOSURE AMOUNT'                            => $value->file_case_details->foreclosure_amount ?? '',

                        'DATE'                                          => '23-06-2025',
                        // 'DATE'                                          => now()->format('d-m-Y'),
                    ];

                    $replaceSummernotePlaceholders = function ($html, $replacements) {
                        foreach ($replacements as $key => $value) {
                            // Escape key for regex
                            $escapedKey = preg_quote($key, '/');

                            // Split into words
                            $words = preg_split('/\s+/', $escapedKey);

                            // Allow tags or spacing between words
                            $pattern = '/\{\{(?:\s|&nbsp;|<[^>]+>)*' . implode('(?:\s|&nbsp;|<[^>]+>)*', $words) . '(?:\s|&nbsp;|<[^>]+>)*\}\}/iu';

                            // Replace using callback
                            $html = preg_replace_callback($pattern, function () use ($value) {
                                return $value;
                            }, $html);
                        }

                        return $html;
                    };

                    $finalNotice = $replaceSummernotePlaceholders($noticeTemplate, $data);

                    // Use full URLs
                    $headerImg    = url('storage/' . $organizationLetterHead_header['header_letterhead']);
                    $footerImg    = url('storage/' . $organizationLetterHead_footer['footer_letterhead']);
                    $signatureImg = url('storage/' . $organizationManager_signature['signature_org']);

                    // Append signature at the end of the notice
                    $finalNotice .= '
                        <div style="text-align: left; margin-top: 10px;">
                            <img src="' . $signatureImg . '" style="height: 80px;" alt="Signature">
                        </div>
                    ';

                    // Now wrap everything in proper HTML with real headers/footers
                    $html = '
                    <html>
                    <head>
                        <style>
                            @page {
                                size: A4;
                                margin: 6mm 12mm 12mm 12mm; /* top, right, bottom, left */
                                header: html_myHeader;
                                footer: html_myFooter;
                            }

                            body {
                                font-family: DejaVu Sans, sans-serif;
                                font-size: 12px;
                                line-height: 1.4;
                            }

                            img {
                                max-width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>

                    <!-- Define actual header -->
                    <htmlpageheader name="myHeader">
                        <img src="' . $headerImg . '" alt="Header Image" />
                    </htmlpageheader>

                    <body>

                    ' . $finalNotice . '

                    </body>

                    <!-- Define actual footer -->
                    <htmlpagefooter name="myFooter">
                        <img src="' . $footerImg . '" alt="Footer Image" />
                    </htmlpagefooter>

                    </html>';

                    // 2. Generate PDF with A4 paper size
                    $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                    // Create temporary PDF file
                    $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                    $pdf->save($tempPdfPath);

                    // Wrap temp file in UploadedFile so it can go through Helper::saveFile
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempPdfPath,
                        'notice_' . time() . '.pdf',
                        'application/pdf',
                        null,
                        true
                    );

                    // Save the PDF using your helper
                    $savedPath = Helper::loannosaveFile($uploadedFile, 'premediationnotices', $value->loan_number);

                    $notice = MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)->update([
                        'notice_copy'   => $savedPath,
                        // 'notice_date'   => now(),
                    ]);

                    //Send Email with Notice for Pre Mediation Notice
                    $data = Setting::where('setting_type', '3')->get()->pluck('filed_value', 'setting_name')->toArray();

                    Config::set("mail.mailers.smtp", [
                        'transport'  => 'smtp',
                        'host'       => $data['smtp_host'],
                        'port'       => $data['smtp_port'],
                        'encryption' => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                        'username'   => $data['smtp_user'],
                        'password'   => $data['smtp_pass'],
                        'timeout'    => null,
                        'auth_mode'  => null,
                    ]);

                    Config::set("mail.from", [
                        'address' => $data['email_from'],
                        'name'    => config('app.name'),
                    ]);

                    // ############# Send Email using Email Address #############
                    if (! empty($value->respondent_email)) {

                        // $email = filter_var($value->respondent_email, FILTER_SANITIZE_EMAIL);
                        $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                        $validator = Validator::make(['email' => $email], [
                            'email' => 'required|email:rfc,dns',
                        ]);

                        if ($validator->fails()) {

                            Log::warning("Invalid email address: $email");
                            MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                ->update([
                                    'email_status' => 2,
                                    'email_bounce_datetime' => $now,
                                ]);

                        } else {

                            $subject     = "Subject: Service of Legal Notice--- {$value->loan_number} (Co-branded with Bajaj Finserv)";
                            $description = "Dear {$value->respondent_first_name} {$value->respondent_last_name},

Please find attached a RECALL NOTICE/ DEMAND NOTICE  addressed to you on behalf of our client, RBL Bank Ltd.

You are requested to peruse the same carefully and take appropriate steps as advised therein.

This email and the attached legal notice are being sent without prejudice to our client’s rights and remedies available in law, all of which are expressly reserved.

Attachment: Legal Notice.pdf
Regards,

Anil  Kumar  Sharma  And  Associates

Advocates And Legal Consultants
LITIGATION | ADVISORY | COMPLIANCE
(M) +91-9414295841/7852891583
EMAIL: advocatejdr@gmail.com
Services Provided by MediateWay ADR Centre LLP, Online Platform";

                            try {
                                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                                    $message->to($email)
                                        ->cc('legaldesk@rblbank.com')
                                        ->subject($subject)
                                        ->attach(public_path(str_replace('\\', '/', 'storage/' . $savedPath)), [
                                            'mime' => 'application/pdf',
                                        ]);
                                });

                                // Success
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                    ->update([
                                        'notice_send_date' => $now,
                                        'email_status'     => 1,
                                    ]);
                                Log::info("Mediation Email sent successfully for FileCase ID: {$fileCaseId}");
                            } catch (\Exception $e) {
                                Log::warning("Mediation Email failed for FileCase ID: {$fileCaseId}. Response: " . $e->getMessage());
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                    ->update([
                                        'email_status' => 2,
                                        'email_bounce_datetime' => $now,
                                    ]);
                            }
                        }
                    }
                } else {
                    
                    //Send Email with Notice for Assign Arbitrator
                    $data = Setting::where('setting_type', '3')->get()->pluck('filed_value', 'setting_name')->toArray();

                    Config::set("mail.mailers.smtp", [
                        'transport'  => 'smtp',
                        'host'       => $data['smtp_host'],
                        'port'       => $data['smtp_port'],
                        'encryption' => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                        'username'   => $data['smtp_user'],
                        'password'   => $data['smtp_pass'],
                        'timeout'    => null,
                        'auth_mode'  => null,
                    ]);

                    Config::set("mail.from", [
                        'address' => $data['email_from'],
                        'name'    => config('app.name'),
                    ]);

                    // ###################################################################
                    // ################# Send Email using Email Address ##################
                    if (! empty($value->respondent_email)) {

                        $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                        $validator = Validator::make(['email' => $email], [
                            'email' => 'required|email:rfc,dns',
                        ]);

                        if ($validator->fails()) {
                            Log::warning("Invalid email address: $email");
                            MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                ->update([
                                    'email_status' => 2,
                                    'email_bounce_datetime' => $now,
                                ]);
                        } else {

                            $subject     = "Subject: Service of Legal Notice--- {$value->loan_number} (Co-branded with Bajaj Finserv)";
                            $description = "Dear {$value->respondent_first_name} {$value->respondent_last_name},

Please find attached a RECALL NOTICE/ DEMAND NOTICE  addressed to you on behalf of our client, RBL Bank Ltd.

You are requested to peruse the same carefully and take appropriate steps as advised therein.

This email and the attached legal notice are being sent without prejudice to our client’s rights and remedies available in law, all of which are expressly reserved.

Attachment: Legal Notice.pdf
Regards,

Anil  Kumar  Sharma  And  Associates

Advocates And Legal Consultants
LITIGATION | ADVISORY | COMPLIANCE
(M) +91-9414295841/7852891583
EMAIL: advocatejdr@gmail.com
Services Provided by MediateWay ADR Centre LLP, Online Platform";

                            try {
                                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                    $message->to($email)
                                        ->cc('legaldesk@rblbank.com')
                                        ->subject($subject)
                                        ->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice_copy)), [
                                            'mime' => 'application/pdf',
                                        ]);
                                });

                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                    ->update([
                                        'notice_send_date' => $now,
                                        'email_status'     => 1,
                                    ]);
                                Log::info("Mediation Email sent successfully for FileCase ID: {$fileCaseId}");
                            } catch (\Exception $e) {
                                Log::warning("Mediation Email failed for FileCase ID: {$fileCaseId}. Response: " . $e->getMessage());
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)
                                    ->update([
                                        'email_status' => 2,
                                        'email_bounce_datetime' => $now,
                                    ]);
                            }
                        }
                    }
                }

            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error sending Mediation email for record ID {$value->id}: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
