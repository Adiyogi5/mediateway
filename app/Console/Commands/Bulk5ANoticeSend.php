<?php
namespace App\Console\Commands;

use App\Models\AssignCase;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Bulk5ANoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-5a-notice';

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
        // Stage 5-A Notice: by Arbitrator through Case Manager 
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
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), notices.notice_date) >= organization_notice_timelines.notice_5a');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 9);
                })
                    ->orWhereHas('notices', function ($q) {
                        $q->where('notice_type', 9)
                            ->where('email_status', 0);
                    });
            })
            ->whereIn('organization_notice_timelines.notice_5a', function ($query) {
                $query->select('notice_5a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->select(
                'file_cases.*',
                'organization_notice_timelines.notice_5a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                $noticeData = Notice::where('file_case_id', $value->id)->where('notice_type', 9)->first();
                $notice = $noticeData->notice;

                $now = now();
             
                if ($noticeData) {
                        FileCaseDetail::where('file_case_id', $value->id)
                            ->update([
                                'stage_5a_notice_date' => $now->format('d-m-Y'),
                            ]);
                    }
               
                if (!empty($caseData)) {
                    $noticetemplateData = NoticeTemplate::where('id', 9)->first();

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
                            $description = $noticetemplateData->description;

                            Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($notice, $subject, $email) {
                                $message->to($email)
                                        ->subject($subject)
                                        ->attach(public_path(str_replace('\\', '/', $notice)), [
                                            'mime' => 'application/pdf',
                                        ]);
                            });

                            // if (Mail::failures()) {
                            Log::error("Failed to send email to: $email");
                            $noticeData->update(['email_status' => 2]);
                            // } else {
                            $noticeData->update(['notice_send_date' => now()]);
                            $noticeData->update(['email_status' => 1]);
                            // }
                        }
                    }

                    // Send SMS Invitation using Twilio
                    // try {
                    //     $sid    = env("TWILIO_ACCOUNT_SID");
                    //     $token  = env("TWILIO_AUTH_TOKEN");
                    //     $sender = env("TWILIO_SENDER");

                    //     $client = new Client($sid, $token);

                    //     $country_data = Country::where('id', $request->country_id)->where('status', 1)->first();
                    //     $phone_code = $country_data->phone_code ?? '';

                    //     $message = "{$user->name} has invited you to join Patrimonial, an online testament and wealth management App, to securely manage and access patrimonial information. Accept the invitation here: https://www.name/login";

                    //     $client->messages->create($phone_code . $request->mobile, [
                    //         'from' => $sender,
                    //         'body' => $message,
                    //     ]);
                    // } catch (\Throwable $th) {
                    //     // Log SMS error but don't stop execution
                    //     Log::error('SMS sending failed: ' . $th->getMessage());
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
