<?php
namespace App\Console\Commands;

use App\Library\TextLocal;
use App\Models\AssignCase;
use App\Models\Country;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
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

class Bulk4ANoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-4a-notice';

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
        // ####################################################
        // Stage 4-A Notice: by Arbitrator through Case Manager
        // ####################################################
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
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_4a');
            })
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 9)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0)
                                   ->orWhere('whatsapp_notice_status', 0)
                                   ->orWhere('sms_status', 0);
                        });
                });
            })
            ->whereIn('organization_notice_timelines.notice_4a', function ($query) {
                $query->select('notice_4a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->where('notices.notice_type', 9)
            ->select(
                'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
                'organization_notice_timelines.notice_4a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(20)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();

                $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                $noticeData     = Notice::where('file_case_id', $value->id)->where('notice_type', 9)->first();
                $notice         = $noticeData->notice;
                $now = now();

                if ($noticeData) {
                    FileCaseDetail::where('file_case_id', $value->id)
                        ->update([
                            'stage_4a_notice_date' => $now->format('Y-m-d'),
                        ]);
                }

                if (! empty($caseData)) {
                    $noticetemplateData = NoticeTemplate::where('id', 9)->first();

                    // #########################################################
                    // ################# Send Email using SMTP #################
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
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                            ->update([
                                                'email_status' => 2,
                                            ]);
                            } else {

                                $subject     = $noticetemplateData->subject;
                                $description = $noticetemplateData->email_content;

                                try {
                                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($notice, $subject, $email) {
                                        $message->to($email)
                                            ->subject($subject)
                                            ->attach(public_path(str_replace('\\', '/', $notice)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                    });

                                            Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                            ->update([
                                                'notice_send_date' => $now,
                                                'email_status' => 1,
                                            ]);

                                        } catch (\Exception $e) {
                                            Log::error("Failed to send email to: $email. Error: " . $e->getMessage());
                                            Notice::where('file_case_id', $value->id)->where('notice_type', 9)
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

                            $message = "Ref: Arbitration Hearing : Loan A/c {$value->loan_number}
Dear {$value->respondent_first_name} {$value->respondent_last_name},
You are notified that the first arbitration hearing is scheduled on [Date] at [Time] via Zoom.
🔹 Meeting ID: [ID]
🔹 Passcode: [Password]
🔹 Link: [Zoom Link]
Submit your written reply/documents via MediateWay portal on or before the hearing.
Failure to appear may lead to ex-parte proceedings (Sec. 25, A&C Act).
(Sole Arbitrator)
{$arbitratorsData->name}";

                            $pdfUrl = public_path(str_replace('\\', '/', 'storage/' . $value->notice));

                            if (! empty($value->respondent_mobile)) {
                                $response = Http::get(config('services.whatsapp.url'), [
                                    'apikey' => config('services.whatsapp.api_key'),
                                    'mobile' => $mobileNumber,
                                    'msg'    => $message,
                                    'pdf'    => $pdfUrl,
                                ]);

                                if ($response->successful()) {
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status' => 1,
                                        ]);
                                    return true;
                                } else {
                                    Log::error('WhatsApp API error: ' . $response->body());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
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
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
