<?php
namespace App\Console\Commands;

use App\Models\AssignCase;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\Notice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bulk3ANoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-whatsapp-3a-notice';

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

        // Join only the 5-type notices to get their details (leftJoin to include cases even when 5-type doesn't exist)
            ->leftJoin('notices as notice5', function ($join) {
                $join->on('notice5.file_case_id', '=', 'file_cases.id')
                    ->where('notice5.notice_type', 5);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_3a');
            })

        // Apply condition for type 5 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 5)
                        ->where(function ($inner) {
                            $inner->Where('whatsapp_notice_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_3a', function ($query) {
                $query->select('notice_3a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice5.notice as notice5',
                'notice5.whatsapp_notice_status as whatsapp_notice_status5',
                'organization_notice_timelines.notice_3a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                $noticeData     = Notice::where('file_case_id', $value->id)->where('notice_type', 5)->first();
                $notice         = $noticeData->notice;
                // $noticedataFetchArbitrator = Notice::where('file_case_id', $value->id)->where('notice_type', 5)->first();
               
                if (($assigncaseData->receiveto_casemanager == 0)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                    $now = now();
                    $fileCaseId = $value->id;

                    Log::info("Stage 3A Notice - Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice5)) {
                        try {
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            $message = "Subject: Proposal for Appointment of Arbitrator
Dear {$value->respondent_first_name} {$value->respondent_last_name},
A case has been filed by {$value->claimant_first_name} {$value->claimant_last_name} under Loan A/c No. {$value->loan_number} at MediateWay ADR Centre for online arbitration.
We propose the following as Sole Arbitrator:
    1. {$arbitratorsData->name}
    2. {$arbitratorsData->name}
    3. {$arbitratorsData->name}
Please confirm your consent to any one within 7 days. No response will be treated as no objection. Arbitration will be held online. Objections to the venue must also be raised within 7 days.
MediateWay ADR Centre";

                            $pdfUrl = url(str_replace('\\', '/', 'public/storage/' . $value->notice5));

                            if (! empty($mobileNumber)) {
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
                                    Log::info("Notice 3A Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                                } else {
                                    Log::warning("Notice 3A Whatsapp failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 5)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                        ]);
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 3A Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 3A Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
