<?php
namespace App\Console\Commands;

use App\Library\TextLocal;
use App\Models\AssignCase;
use App\Models\Country;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use App\Models\SmsCount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class Bulk3ANoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-3a-notice';

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
        // ##################################################
        // Appointment Of Multiple Arbitrator - 3A - Notice Send
        // ##################################################
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
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_3a');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 5);
                })->orWhereHas('notices', function ($q) {
                    $q->where('notice_type', 5)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0)
                                ->orWhere('whatsapp_notice_status', 0)
                                ->orWhere('sms_status', 0);
                        });
                });
            })
            ->whereIn('organization_notice_timelines.notice_3a', function ($query) {
                $query->select('notice_3a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->where('notices.notice_type', 5)
            ->select(
                'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
                'organization_notice_timelines.notice_3a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(20)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                $noticeData     = Notice::where('file_case_id', $value->id)->where('notice_type', 5)->first();
                $notice         = $noticeData->notice;
                // $noticedataFetchArbitrator = Notice::where('file_case_id', $value->id)->where('notice_type', 5)->first();
                // dd($noticedataFetchArbitrator);
                if (($assigncaseData->receiveto_casemanager == 0)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $noticetemplateData = NoticeTemplate::where('id', 5)->first();
                    $noticeTemplate     = $noticetemplateData->notice_format;

                    $now = now();

                    //##### This pdf creation in case assign controller - in updateassigndetail function for appoint 3 arbitrators
                    //############################################################################################################
                    // // Define your replacement values
                    // $data = [
                    //     "ARBITRATOR'S NAME"                                               => $arbitratorsName ?? '',
                    //     "CASE MANAGER'S NAME"                                             => $casemanagerData->name ?? '',
                    //     'PHONE NUMBER'                                                    => $casemanagerData->mobile ?? '',
                    //     'EMAIL ADDRESS'                                                   => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                    //     'CASE REGISTRATION NUMBER'                                        => $value->case_number ?? '',
                    //     'BANK/ORGANISATION/CLAIMANT NAME'                                 => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                    //     'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS'                   => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                    //     'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'                    => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                    //     "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"                    => $casvalueeData->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                    //     'LOAN NO'                                                         => $value->loan_number ?? '',
                    //     'AGREEMENT DATE'                                                  => $value->agreement_date ?? '',
                    //     'ARBITRATION CLAUSE NO'                                           => $value->arbitration_clause_no ?? '',
                    //     'FINANCE AMOUNT'                                                  => $value->file_case_details->finance_amount ?? '',
                    //     'TENURE'                                                          => $value->file_case_details->tenure ?? '',
                    //     'FORECLOSURE AMOUNT'                                              => $value->file_case_details->foreclosure_amount ?? '',

                    //     "ARBITRATOR'S NAME"                                               => $arbitratorsData->name ?? '',
                    //     "ARBITRATOR'S SPECIALIZATION"                                     => $arbitratorsData->specialization ?? '',
                    //     "ARBITRATOR'S ADDRESS"                                            => ($arbitratorsData->address1 ?? '') . '&nbsp;' . ($arbitratorsData->address2 ?? ''),

                    //     'CUSTOMER NAME'                                                   => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                    //     'CUSTOMER ADDRESS'                                                => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                    //     'CUSTOMER MOBILE NO'                                              => $value->respondent_mobile ?? '',
                    //     'CUSTOMER MAIL ID'                                                => $value->respondent_email ?? '',

                    //     'DATE'                                                            => now()->format('d-m-Y'),
                    //     'STAGE 1 NOTICE DATE'                                             => $value->file_case_details->stage_1_notice_date ?? '',
                    //     'STAGE 2B NOTICE DATE'                                            => $value->file_case_details->stage_2b_notice_date ?? '',
                    //     'STAGE 3A NOTICE DATE'                                            => now()->format('d-m-Y'),
                    // ];

                    // $replaceSummernotePlaceholders = function ($html, $replacements) {
                    //     foreach ($replacements as $key => $value) {
                    //         // Escape key for regex
                    //         $escapedKey = preg_quote($key, '/');

                    //         // Split into words
                    //         $words = preg_split('/\s+/', $escapedKey);

                    //         // Allow tags or spacing between words
                    //         $pattern = '/\{\{(?:\s|&nbsp;|<[^>]+>)*' . implode('(?:\s|&nbsp;|<[^>]+>)*', $words) . '(?:\s|&nbsp;|<[^>]+>)*\}\}/iu';

                    //         // Replace using callback
                    //         $html = preg_replace_callback($pattern, function () use ($value) {
                    //             return $value;
                    //         }, $html);
                    //     }

                    //     return $html;
                    // };

                    // $finalNotice = $replaceSummernotePlaceholders($noticeTemplate, $data);

                    // $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                    // // Append the signature image at the end of the content, aligned right
                    // $finalNotice .= '
                    //     <div style="text-align: right; margin-top: 0px;">
                    //         <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                    //     </div>
                    // ';

                    // // 1. Prepare your HTML with custom styles
                    // $html = '
                    // <style>
                    //     @page {
                    //         size: A4;
                    //         margin: 12mm;
                    //     }
                    //     body {
                    //         font-family: DejaVu Sans, sans-serif;
                    //         font-size: 12px;
                    //         line-height: 1.4;
                    //     }
                    //     p {
                    //         margin: 0px 0;
                    //         padding: 0;
                    //     }
                    //     img {
                    //         max-width: 100%;
                    //         height: auto;
                    //     }
                    // </style>
                    // ' . $finalNotice;

                    // // 2. Generate PDF with A4 paper size
                    // $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                    // // Create temporary PDF file
                    // $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                    // $pdf->save($tempPdfPath);

                    // // Wrap temp file in UploadedFile so it can go through Helper::saveFile
                    // $uploadedFile = new \Illuminate\Http\UploadedFile(
                    //     $tempPdfPath,
                    //     'notice_' . time() . '.pdf',
                    //     'application/pdf',
                    //     null,
                    //     true
                    // );

                    // // Save the PDF using your helper
                    // $savedPath = Helper::saveFile($uploadedFile, 'notices');

                    // $now = now();

                    // $notice = Notice::create([
                    //     'file_case_id'               => $value->id,
                    //     'notice_type'                => 5,
                    //     'notice'                     => $savedPath,
                    //     'notice_date'                => now(),
                    //     'notice_send_date'           => null,
                    //     'email_status'               => 0,
                    //     'whatsapp_status'            => 0,
                    //     'whatsapp_notice_status'     => 0,
                    //     'whatsapp_dispatch_datetime' => null,
                    // ]);

                    // if ($notice) {
                    //     FileCaseDetail::where('file_case_id', $notice->file_case_id)
                    //         ->update([
                    //             'stage_3a_notice_date' => $now->format('Y-m-d'),
                    //         ]);
                    // }

                    if ($value->email_status == 0) {
                        //Send Notice for Assign Arbitrator
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

                        if (! empty($value->respondent_email)) {
                            $email = filter_var($value->respondent_email, FILTER_SANITIZE_EMAIL);

                            $validator = Validator::make(['email' => $email], [
                                'email' => 'required|email:rfc,dns',
                            ]);

                            if ($validator->fails()) {

                                Log::warning("Invalid email address: $email");
                                $noticeData->update(['email_status' => 2]);

                            } else {

                                $subject     = $noticetemplateData->subject;
                                $description = $noticetemplateData->email_content;

                                try {
                                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                        $message->to($email)
                                            ->subject($subject)
                                            ->attach(public_path(str_replace('\\', '/', $value->notice)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                    });
                                        Notice::where('file_case_id', $value->id)->where('notice_type', 5)
                                            ->update([
                                                'notice_send_date' => $now,
                                                'email_status' => 1,
                                            ]);

                                        } catch (\Exception $e) {
                                            Log::error("Failed to send email to: $email. Error: " . $e->getMessage());
                                            Notice::where('file_case_id', $value->id)->where('notice_type', 5)
                                            ->update([
                                                'email_status' => 2,
                                            ]);
                                        }
                            }
                        }
                    }


                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if ($value->whatsapp_notice_status == 0 && !empty($value->notice)) {
                        try {
                            $mobileNumber = $value->respondent_mobile;

                            $message = "Subject: Proposal for Appointment of Arbitrator
Dear {$value->respondent_first_name} {$value->respondent_last_name},
A case has been filed by {$value->claimant_first_name} {$value->claimant_last_name} under Loan A/c No. {$value->loan_number} at MediateWay ADR Centre for online arbitration.
We propose the following as Sole Arbitrator:
    1. {$arbitratorsData->name}
    2. {$arbitratorsData->name}
    3. {$arbitratorsData->name}
Please confirm your consent to any one within 7 days. No response will be treated as no objection. Arbitration will be held online. Objections to the venue must also be raised within 7 days.
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
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 5)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status' => 1,
                                        ]);
                                    return true;
                                } else {
                                    Log::error('WhatsApp API error: ' . $response->body());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 5)
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
                    if ($value->sms_status == 0){
                        if (! empty($value->respondent_mobile)) {
                            $approved_sms_count = SmsCount::where('count', '>', 0)->first();

                            if (! $approved_sms_count) {
                                return response()->json([
                                    'status'  => false,
                                    'message' => "Message can't be sent because your SMS quota is empty.",
                                ], 422);
                            }

                            $mobile = preg_replace('/\D/', '', trim($value->respondent_mobile));
                            $mobilemessage =  "Hello User Your Login Verification Code is $otp. Thanks AYT";
                            try {
                                $smsResponse = TextLocal::sendSms(['+91' . $mobile], $mobilemessage);

                                if ($smsResponse) {
                                    $approved_sms_count->decrement('count');

                                    return response()->json([
                                        'status'  => true,
                                        'message' => 'Message sent successfully to your mobile!',
                                        'data'    => '',
                                    ]);
                                } else {
                                    return response()->json([
                                        'status'  => false,
                                        'message' => "Message couldn't be sent, please retry later.",
                                        'data'    => '',
                                    ], 422);
                                }
                            } catch (\Exception $e) {
                                Log::error('SMS send failed: ' . $e->getMessage());

                                return response()->json([
                                    'status'  => false,
                                    'message' => 'An error occurred while sending SMS.',
                                ], 500);
                            }
                        }
                    }
                }
            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error sending email for record ID {$value->id}: " . $th->getMessage());
                // $noticeData->update(['email_status' => 2]);
            }
        }
    }
}
