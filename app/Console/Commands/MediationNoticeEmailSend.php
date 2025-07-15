<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\MediationNotice;
use App\Models\MediatorMeetingRoom;
use App\Models\Drp;
use App\Models\FileCase;
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

class MediationNoticeEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:mediation-notice-email-send';

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
        // ########################################################
        // Mediation Notice Send Via Email - By Case Manager
        // ########################################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->leftJoin('mediation_notices', 'mediation_notices.file_case_id', '=', 'file_cases.id')
            ->where('mediation_notices.mediation_notice_type', 2)
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
            ->limit(4)
            ->get();
       
        foreach ($caseData as $key => $value) {
            try {

                $noticetemplateData = NoticeTemplate::where('id', 12)->first();
                $noticeTemplate     = $noticetemplateData->notice_format;

                $assigncaseData = AssignCase::where('case_id', $value->id)->first();

                $mediatorName = '';
                $caseManagerName = '';
                $meettingData    = null;

                if ($assigncaseData) {
                    $mediatorName       = Drp::where('id', $assigncaseData->mediator_id)->value('name');
                    $caseManagerName    = Drp::where('id', $assigncaseData->case_manager_id)->value('name');
                    $caseManagerMobile  = Drp::where('id', $assigncaseData->case_manager_id)->value('mobile');
                    $caseManagerEmail   = Drp::where('id', $assigncaseData->case_manager_id)->value('email');
                    $caseManagerSpecialization   = Drp::where('id', $assigncaseData->case_manager_id)->value('specialization');

                    $meettingData = MediatorMeetingRoom::where('mediator_id', $assigncaseData->mediator_id)
                                        ->whereRaw('FIND_IN_SET(?, meeting_room_case_id)', [$value->id])
                                        ->first();

                    $room_id = optional($meettingData)->room_id;
                    $meetingLink = $room_id
                            ? route('front.guest.livemediatormeetingroom', ['room_id' => $room_id])
                            : 'Meeting link not available';
                }

                $settingdata        = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                $now                = now();
                
                $fileCaseId = $value->id;
                Log::info("Processing Mediation Notice For Meeting - Email for FileCase ID: {$fileCaseId}");

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
                        "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"  => $value->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                        'CASE REGISTRATION NUMBER'                      => $value->case_number ?? '',
                        'LOAN NO'                                       => $value->loan_number ?? '',
                        'FORECLOSURE AMOUNT'                            => $value->file_case_details->foreclosure_amount ?? '',
                        'FORECLOSURE DATE'                              => $value->file_case_details->foreclosure_amount_date ?? '',
                        'AGREEMENT DATE'                                => $value->agreement_date ?? '',
                        'FINANCE AMOUNT'                                => $value->file_case_details->finance_amount ?? '',
                        'TENURE'                                        => $value->file_case_details->tenure ?? '',

                        'PRODUCT'                                       => $value->file_case_details->product ?? '',

                        'MEDIATORS NAME'                                => $mediatorName ?? '',
                        'CASEMANAGERS NAME'                             => $caseManagerName ?? '',
                        'DESIGNATION'                                   => $caseManagerSpecialization ?? '',

                        'CONTACT INFORMATION'                           => $settingdata['mediateway_letterhead'] . '/' . $settingdata['mediateway_letterhead'] ?? '',
                        'MODE OF MEETING'                               => 'Digital Room Platform - Mediateway ADR Centre',
                        'MEETING LINK'                                  => $meetingLink ?? '',

                        'BRIEF OF CASE'                                 => $meetingLink ?? '',

                        'ONLINE MEETING DATE'                           => $meettingData->date ?? '',
                        'ONLINE MEETING TIME'                           => $meettingData->time ?? '',

                        'MEDIATEWAY MOBILE NUMBER'                      => $settingdata['phone'],
                        'MEDIATEWAY EMAIL'                              => $settingdata['email'],
                        'DATE'                                          => now()->format('d-m-Y'),
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
                    $headerImg    = url('storage/' . $settingdata['mediateway_letterhead']);
                    $signatureImg = url('storage/' . $settingdata['mediateway_signature']);

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
                    $savedPath = Helper::loannosaveFile($uploadedFile, 'mediationnotices', $value->loan_number);

                    $notice = MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)->update([
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
                            MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
                                ->update([
                                    'email_status' => 2,
                                    'email_bounce_datetime' => $now,
                                ]);

                        } else {

                            $subject     = "Urgent: Invitation for Online Mediation Meeting – [Loan Account No. {$value->loan_number}]";
                            $description = "Dear {$value->respondent_first_name} {$value->respondent_last_name},

We are writing to formally invite you to participate in an Online Mediation Meeting under Clause 6 of the Mediation Bill, 2021, regarding the dispute between you and {$value->claimant_first_name} {$value->claimant_last_name} related to your CC / Loan Account No. {$value->loan_number}.
Case Details:
• Case Reference Number: {$value->case_number}
• Date: {$meettingData->date}
• Time: {$meettingData->time}
• Mode: Online via Digital Room Platform - Mediateway ADR Centre
• Meeting Link: {$meetingLink}
• Mediator(s): {$mediatorName}

You have availed a financial facility from {$value->respondent_first_name} {$value->respondent_last_name} under the {$value->file_case_details->product} loan agreement, but as per the records, you have defaulted on your payment obligations. The outstanding amount as of today is Rs. {$value->file_case_details->foreclosure_amount}, which includes overdue charges and penalties.

Despite this, {$value->respondent_first_name} {$value->respondent_last_name} is providing you with this final opportunity to resolve the matter amicably before initiating legal action in a competent court.

We encourage your active participation in this mediation meeting to reach a mutually agreeable and fair resolution.

If you have any questions or require assistance, please contact your Case Manager, Mr. {$caseManagerName}, at {$caseManagerMobile}/{$caseManagerEmail}.

We look forward to your cooperation.

Yours sincerely,
MediateWay ADR Centre
Contact Information: [{$settingdata['phone']}/{$settingdata['email']}]";

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
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
                                    ->update([
                                        'notice_send_date' => $now,
                                        'email_status'     => 1,
                                    ]);
                                Log::info("Mediation Email sent successfully for FileCase ID: {$fileCaseId}");
                            } catch (\Exception $e) {
                                Log::warning("Mediation Email failed for FileCase ID: {$fileCaseId}. Response: " . $e->getMessage());
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
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
                            MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
                                ->update([
                                    'email_status' => 2,
                                    'email_bounce_datetime' => $now,
                                ]);
                        } else {

                            $subject     = "Urgent: Invitation for Online Mediation Meeting – [Loan Account No. {$value->loan_number}]";
                            $description = "Dear {$value->respondent_first_name} {$value->respondent_last_name},

We are writing to formally invite you to participate in an Online Mediation Meeting under Clause 6 of the Mediation Bill, 2021, regarding the dispute between you and {$value->claimant_first_name} {$value->claimant_last_name} related to your CC / Loan Account No. {$value->loan_number}.
Case Details:
• Case Reference Number: {$value->case_number}
• Date: {$meettingData->date}
• Time: {$meettingData->time}
• Mode: Online via Digital Room Platform - Mediateway ADR Centre
• Meeting Link: {$meetingLink}
• Mediator(s): {$mediatorName}

You have availed a financial facility from {$value->respondent_first_name} {$value->respondent_last_name} under the {$value->file_case_details->product} loan agreement, but as per the records, you have defaulted on your payment obligations. The outstanding amount as of today is Rs. {$value->file_case_details->foreclosure_amount}, which includes overdue charges and penalties.

Despite this, {$value->respondent_first_name} {$value->respondent_last_name} is providing you with this final opportunity to resolve the matter amicably before initiating legal action in a competent court.

We encourage your active participation in this mediation meeting to reach a mutually agreeable and fair resolution.

If you have any questions or require assistance, please contact your Case Manager, Mr. {$caseManagerName}, at {$caseManagerMobile}/{$caseManagerEmail}.

We look forward to your cooperation.

Yours sincerely,
MediateWay ADR Centre
Contact Information: [{$settingdata['phone']}/{$settingdata['email']}]";

                            try {
                                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                    $message->to($email)
                                        ->cc('legaldesk@rblbank.com')
                                        ->subject($subject)
                                        ->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice_copy)), [
                                            'mime' => 'application/pdf',
                                        ]);
                                });

                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
                                    ->update([
                                        'notice_send_date' => $now,
                                        'email_status'     => 1,
                                    ]);
                                Log::info("Mediation Email sent successfully for FileCase ID: {$fileCaseId}");
                            } catch (\Exception $e) {
                                Log::warning("Mediation Email failed for FileCase ID: {$fileCaseId}. Response: " . $e->getMessage());
                                MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 2)
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
