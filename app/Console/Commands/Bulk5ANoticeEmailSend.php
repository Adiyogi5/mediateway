<?php
namespace App\Console\Commands;

use App\Library\TextLocal;
use App\Models\AssignCase;
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

class Bulk5ANoticeEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-email-5a-notice';

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
        // $caseData = FileCase::with('file_case_details')
        //     ->join(DB::raw("(
        //         SELECT
        //             id AS org_id,
        //             name AS org_name,
        //             IF(parent_id = 0, id, parent_id) AS effective_parent_id,
        //             IF(parent_id = 0, name,
        //                 (SELECT name FROM organizations AS parent_org WHERE parent_org.id = organizations.parent_id)
        //             ) AS effective_parent_name
        //         FROM organizations
        //     ) AS org_with_parent"), 'org_with_parent.org_id', '=', 'file_cases.organization_id')
        //     ->join('organization_lists', 'org_with_parent.effective_parent_name', '=', 'organization_lists.name')
        //     ->join('organization_notice_timelines', 'organization_notice_timelines.organization_list_id', '=', 'organization_lists.id')
        //     ->join('notices', 'notices.file_case_id', '=', 'file_cases.id')
        //     ->whereHas('notices', function ($query) {
        //         $query->where('notice_type', 1)
        //             ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_5a');
        //     })
        //     ->where(function ($query) {
        //         $query->WhereHas('notices', function ($q) {
        //             $q->where('notice_type', 10)
        //                 ->where(function ($inner) {
        //                     $inner->where('email_status', 0)
        //                         ->orWhere('whatsapp_notice_status', 0)
        //                         ->orWhere('sms_status', 0);
        //                 });
        //         });
        //     })
        //     ->whereIn('organization_notice_timelines.notice_5a', function ($query) {
        //         $query->select('notice_5a')
        //             ->from('organization_notice_timelines')
        //             ->whereNull('deleted_at')
        //             ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
        //     })
        //     ->where('notices.notice_type', 10)
        //     ->select(
        //         'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
        //         'organization_notice_timelines.notice_5a',
        //         DB::raw('org_with_parent.effective_parent_id as parent_id'),
        //         DB::raw('org_with_parent.effective_parent_name as parent_name')
        //     )
        //     ->distinct()
        //     ->limit(20)
        //     ->get();

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

        // Join only the 10-type notices to get their details (leftJoin to include cases even when 10-type doesn't exist)
            ->leftJoin('notices as notice10', function ($join) {
                $join->on('notice10.file_case_id', '=', 'file_cases.id')
                    ->where('notice10.notice_type', 10);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_5a');
            })

        // Apply condition for type 10 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 10)
                        ->where(function ($inner) {
                            $inner->Where('email_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_5a', function ($query) {
                $query->select('notice_5a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice10.notice as notice10',
                'notice10.email_status as email_status10',
                'organization_notice_timelines.notice_5a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();

                $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                $noticeData = Notice::where('file_case_id', $value->id)->where('notice_type', 10)->first();
                $notice     = $noticeData->notice;
                $now        = now();

                $fileCaseId = $value->id;
                Log::info("Processing Stage 5A Notice - Email for FileCase ID: {$fileCaseId}");

                if ($noticeData) {
                    FileCaseDetail::where('file_case_id', $value->id)
                        ->update([
                            'stage_5a_notice_date' => $now->format('Y-m-d'),
                        ]);
                }

                if (! empty($caseData)) {
                    $noticetemplateData = NoticeTemplate::where('id', 10)->first();

                    // #########################################################
                    // ################# Send Email using SMTP #################
                    if ($value->email_status10 == 0) {
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

                        // ###################################################################
                        // ################# Send Email using Email Address ##################
                        if (! empty($value->respondent_email)) {
                            $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                            $validator = Validator::make(['email' => $email], [
                                'email' => 'required|email:rfc,dns',
                            ]);

                            if ($validator->fails()) {
                                Log::warning("Invalid email address: $email");
                                Notice::where('file_case_id', $value->id)->where('notice_type', 10)
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
                                        // ->attach(public_path(str_replace('\\', '/', $notice)), [
                                        //     'mime' => 'application/pdf',
                                        // ]);
                                            ->attach(public_path(str_replace('\\', '/', 'storage/' . $notice)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                    });

                                    Notice::where('file_case_id', $value->id)->where('notice_type', 10)
                                        ->update([
                                            'notice_send_date' => $now,
                                            'email_status'     => 1,
                                        ]);
                                    Log::info("Stage 5A Email sent successfully for FileCase ID: {$fileCaseId}");
                                } catch (\Exception $e) {
                                    Log::error("Notice 5A Failed to send email to: $email. FileCase ID: {$fileCaseId}. Error: " . $e->getMessage());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 10)
                                        ->update([
                                            'email_status' => 2,
                                        ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error sending Notice 5A email for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
