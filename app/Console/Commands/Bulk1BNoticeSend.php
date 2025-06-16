<?php
namespace App\Console\Commands;

use App\Models\FileCase;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Bulk1BNoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-1b-notice';

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
        // Appointment Of Case Manager - 1B - Notice Send
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
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_1b');
            })
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 3)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0)
                                ->orWhere('whatsapp_notice_status', 0)
                                ->orWhere('sms_status', 0);
                        });
                });
            })
            ->whereIn('organization_notice_timelines.notice_1b', function ($query) {
                $query->select('notice_1b')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->where('notices.notice_type', 3)
            ->select(
                'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
                'organization_notice_timelines.notice_1b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(20)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $noticetemplateData = NoticeTemplate::where('id', 3)->first();
                $now                = now();

                // #########################################################
                // ################# Send Email using SMTP #################
                if ($value->email_status == 0) {

                    if (! empty($value->notice)) {
                        //Send Email with Notice
                        Config::set("mail.mailers.smtp", [
                            'transport'  => 'smtp',
                            'host'       => 'smtp.gmail.com',
                            'port'       => '587',
                            'encryption' => in_array((int) '587', [587, 465]) ? 'tls' : 'ssl',
                            'username'   => 'advocatejdr@gmail.com',
                            'password'   => 'JOLYLLB2005',
                            'timeout'    => null,
                            'auth_mode'  => null,
                        ]);

                        Config::set("mail.from", [
                            'address' => 'advocatejdr@gmail.com',
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
                                Notice::where('file_case_id', $value->id)->where('notice_type', 3)
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

                                    Notice::where('file_case_id', $value->id)->where('notice_type', 3)
                                        ->update([
                                            'notice_send_date' => $now,
                                            'email_status'     => 1,
                                        ]);

                                } catch (\Exception $e) {
                                    Log::error("Failed to send email to: $email. Error: " . $e->getMessage());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 3)
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

                        $message = "Subject: Invocation of Arbitration : Loan A/c No. {$value->loan_number}
Despite notice dated {$value->file_case_details->stage_1_notice_date}, dues of  ₹{$value->file_case_details->foreclosure_amount} remain unpaid. As per Clause {$value->arbitration_clause_no} of the Loan Agreement dated {$value->agreement_date}, arbitration is now invoked.
MediateWay ADR Centre is appointed to conduct the proceedings.
📍 Plot No. 173-A, S-1, 2nd Floor, Narayan Dham-6th, Kalwar Road, Jaipur : 302012
📞 9461165841
📧 mediatewayinfo@gmail.com
Please respond within 7 days to avoid legal action.
{$value->claimant_first_name} {$value->claimant_last_name}";

                        $pdfUrl = public_path(str_replace('\\', '/', 'storage/' . $value->notice));

                        if (! empty($value->respondent_mobile)) {
                            $response = Http::get(config('services.whatsapp.url'), [
                                'apikey' => config('services.whatsapp.api_key'),
                                'mobile' => $mobileNumber,
                                'msg'    => $message,
                                'pdf'    => $pdfUrl,
                            ]);

                            if ($response->successful()) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 3)
                                    ->update([
                                        'whatsapp_dispatch_datetime' => $now,
                                        'whatsapp_notice_status'     => 1,
                                    ]);
                                return true;
                            } else {
                                Log::error('WhatsApp API error: ' . $response->body());
                                Notice::where('file_case_id', $value->id)->where('notice_type', 3)
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
                if ($value->sms_status == 0) {
                    // if (! empty($value->respondent_mobile)) {
                    //     $approved_sms_count = SmsCount::where('count', '>', 0)->first();

                    //     if (! $approved_sms_count) {
                    //         return response()->json([
                    //             'status'  => false,
                    //             'message' => "Message can't be sent because your SMS quota is empty.",
                    //         ], 422);
                    //     }

                    //     $mobile = preg_replace('/\D/', '', trim($value->respondent_mobile));
                    //     $mobilemessage =  "Hello User Your Login Verification Code is $otp. Thanks AYT";
                    //     try {
                    //         $smsResponse = TextLocal::sendSms(['+91' . $mobile], $mobilemessage);

                    //         if ($smsResponse) {
                    //             $approved_sms_count->decrement('count');

                    //             return response()->json([
                    //                 'status'  => true,
                    //                 'message' => 'Message sent successfully to your mobile!',
                    //                 'data'    => '',
                    //             ]);
                    //         } else {
                    //             return response()->json([
                    //                 'status'  => false,
                    //                 'message' => "Message couldn't be sent, please retry later.",
                    //                 'data'    => '',
                    //             ], 422);
                    //         }
                    //     } catch (\Exception $e) {
                    //         Log::error('SMS send failed: ' . $e->getMessage());

                    //         return response()->json([
                    //             'status'  => false,
                    //             'message' => 'An error occurred while sending SMS.',
                    //         ], 500);
                    //     }
                    // }
                }

            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error sending email for record ID {$value->id}: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
