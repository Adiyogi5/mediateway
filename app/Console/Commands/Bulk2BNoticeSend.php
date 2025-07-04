<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Bulk2BNoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-2b-notice';

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
        // ##############################################
        // Appointment Of Case Manager - 2B - Notice Send
        // ##############################################
        $caseData = FileCase::with('file_case_details')
            ->join(DB::raw("(
                SELECT
                    id AS org_id,
                    name AS org_name,
                    IF(parent_id = 0, id, parent_id) AS effective_parent_id,
                    IF(parent_id = 0, name,
                        (SELECT name FROM organizations AS parent_org WHERE parent_org.id = organizations.parent_id)
                    ) AS effective_parent_name
                FROM organizations
            ) AS org_with_parent"), 'org_with_parent.org_id', '=', 'file_cases.organization_id')
            ->join('organization_lists', 'org_with_parent.effective_parent_name', '=', 'organization_lists.name')
            ->join('organization_notice_timelines', 'organization_notice_timelines.organization_list_id', '=', 'organization_lists.id')
            ->join('notices', 'notices.file_case_id', '=', 'file_cases.id')
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_2b');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 4);
                })->orWhereHas('notices', function ($q) {
                    $q->where('notice_type', 4)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0)
                                ->orWhere('whatsapp_notice_status', 0)
                                ->orWhere('sms_status', 0);
                        });
                });
            })
            ->whereIn('organization_notice_timelines.notice_2b', function ($query) {
                $query->select('notice_2b')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->where('notices.notice_type', 4)
            ->select(
                'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
                'organization_notice_timelines.notice_2b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(20)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchCaseManager = Notice::where('file_case_id', $value->id)->where('notice_type', 4)->first();

                if (! empty($assigncaseData->case_manager_id)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $noticetemplateData = NoticeTemplate::where('id', 4)->first();
                    $noticeTemplate     = $noticetemplateData->notice_format;
                    $now                = now();

                    // #########################################################
                    // ################# Send Email using SMTP #################
                    if ($value->email_status == 0) {

                        if (empty($value->notice)) {
                            // Define your replacement values
                            $data = [
                                "ARBITRATOR'S NAME"                             => $arbitratorsName ?? '',
                                "CASE MANAGER'S NAME"                           => $casemanagerData->name ?? '',
                                'PHONE NUMBER'                                  => $casemanagerData->mobile ?? '',
                                'EMAIL ADDRESS'                                 => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                                'CASE REGISTRATION NUMBER'                      => $value->case_number ?? '',
                                'BANK/ORGANISATION/CLAIMANT NAME'               => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                                'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                                'CUSTOMER NAME'                                 => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                                'CUSTOMER ADDRESS'                              => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                                'CUSTOMER MOBILE NO'                            => $value->respondent_mobile ?? '',
                                'CUSTOMER MAIL ID'                              => $value->respondent_email ?? '',

                                'ARBITRATION CLAUSE NO'                         => $value->arbitration_clause_no ?? '',

                                'DATE'                                          => now()->format('d-m-Y'),
                                'STAGE 2B NOTICE DATE'                          => now()->format('d-m-Y'),
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

                            $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                            // Append the signature image at the end of the content, aligned right
                            $finalNotice .= '
                            <div style="text-align: right; margin-top: 0px;">
                                <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                            </div>
                            ';

                            // 1. Prepare your HTML with custom styles
                            $html = '
                            <style>
                                @page {
                                    size: A4;
                                    margin: 12mm;
                                }
                                body {
                                    font-family: DejaVu Sans, sans-serif;
                                    font-size: 12px;
                                    line-height: 1.4;
                                }
                                p {
                                    margin: 0px 0;
                                    padding: 0;
                                }
                                img {
                                    max-width: 100%;
                                    height: auto;
                                }
                            </style>
                            ' . $finalNotice;

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
                            $savedPath = Helper::saveFile($uploadedFile, 'notices');

                            $notice = Notice::create([
                                'file_case_id'               => $value->id,
                                'notice_type'                => 4,
                                'notice'                     => $savedPath,
                                'notice_date'                => now(),
                                'notice_send_date'           => null,
                                'email_status'               => 0,
                                'whatsapp_status'            => 0,
                                'whatsapp_notice_status'     => 0,
                                'whatsapp_dispatch_datetime' => null,
                            ]);

                            if ($notice) {
                                FileCaseDetail::where('file_case_id', $notice->file_case_id)
                                    ->update([
                                        'stage_2b_notice_date' => $now->format('Y-m-d'),
                                    ]);
                            }
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

                            // ############# Send Email using Email Address #############
                            if (! empty($value->respondent_email)) {

                                $email = filter_var($value->respondent_email, FILTER_SANITIZE_EMAIL);

                                $validator = Validator::make(['email' => $email], [
                                    'email' => 'required|email:rfc,dns',
                                ]);

                                if ($validator->fails()) {

                                    Log::warning("Invalid email address: $email");
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                        ->update([
                                            'email_status' => 2,
                                        ]);

                                } else {

                                    $subject     = $noticetemplateData->subject;
                                    $description = $noticetemplateData->email_content;

                                    try {
                                        Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                                            $message->to($email)
                                                ->subject($subject)
                                                ->attach(public_path(str_replace('\\', '/', 'storage/' . $savedPath)), [
                                                    'mime' => 'application/pdf',
                                                ]);
                                        });

                                        // Success
                                        Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                            ->update([
                                                'notice_send_date' => $now,
                                                'email_status'     => 1,
                                            ]);

                                    } catch (\Exception $e) {
                                        Log::error("Failed to send email to: $email. Error: " . $e->getMessage());
                                        Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                            ->update([
                                                'email_status' => 2,
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

                                $email = filter_var($value->respondent_email, FILTER_SANITIZE_EMAIL);

                                $validator = Validator::make(['email' => $email], [
                                    'email' => 'required|email:rfc,dns',
                                ]);

                                if ($validator->fails()) {
                                    Log::warning("Invalid email address: $email");
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                        ->update([
                                            'email_status' => 2,
                                        ]);
                                } else {

                                    $subject     = $noticetemplateData->subject;
                                    $description = $noticetemplateData->email_content;

                                    try {
                                        Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                            $message->to($email)
                                                ->subject($subject)
                                                ->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice)), [
                                                    'mime' => 'application/pdf',
                                                ]);
                                        });

                                        Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                            ->update([
                                                'notice_send_date' => $now,
                                                'email_status'     => 1,
                                            ]);

                                    } catch (\Exception $e) {
                                        Log::error("Failed to send email to: $email. Error: " . $e->getMessage());
                                        Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                            ->update([
                                                'email_status' => 2,
                                            ]);
                                    }
                                }
                            }
                        }
                    }

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if ($value->whatsapp_notice_status == 0 && ! empty($value->notice)) {
                        try {
                            $settingdata  = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = $value->respondent_mobile;

                            $message = "Dear {$value->respondent_first_name} {$value->respondent_last_name},
                            A case has been registered by {$value->claimant_first_name} {$value->claimant_last_name} against you at MediateWay ADR Centre under Clause {$value->arbitration_clause_no} of your loan agreement for online arbitration as per the Arbitration & Conciliation Act, 1996.
Case Manager:
Name: {$casemanagerData->name}
Ph: {$settingdata['phone']} | Email: {$settingdata['email']}
For details, visit: https://mediateway.com/
MediateWay ADR Centre";

                            $pdfUrl = public_path(str_replace('\\', '/', 'storage/' . $value->notice));

                            if (! empty($value->respondent_mobile)) {
                                $response = Http::get(config('services.whatsapp.url'), [
                                    'apikey' => config('services.whatsapp.api_key'),
                                    'mobile' => $mobileNumber,
                                    'msg'    => $message,
                                    'pdf'    => $pdfUrl,
                                ]);

                                if ($response->successful()) {
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status'     => 1,
                                        ]);
                                    return true;
                                } else {
                                    Log::error('WhatsApp API error: ' . $response->body());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                        ]);
                                    return false;
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error('WhatsApp sending failed: ' . $th->getMessage());
                            // $notice->update(['whatsapp_notice_status' => 2]);
                        }
                    }

                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                    if ($value->sms_status == 0 && ! empty($value->respondent_mobile)) {
                        
                        $mobile        = '91' . preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $smsmessage = "Subject: Intimation of Case Registration – MediateWay ADR Centre. Dear {$value->respondent_first_name}, A case has been registered by {$value->claimant_first_name} against you at MediateWay ADR Centre under Clause of your loan agreement/credit card facility form for online arbitration as per the A & C Act, 1996. For details, visit: https://mediateway.com Team Mediateway";

                        try {
                            $response = Http::post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007641700544394847",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);

                            if ($response->json('isSuccess')) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                return true;
                            } else {
                                Log::error('SMS API error: ' . $response->body());
                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                                return false;
                            }
                        } catch (\Throwable $th) {
                            Log::error('SMS send failed: ' . $th->getMessage());
                            return false;
                        }
                    }
                }
            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error sending email for record ID {$value->id}: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
