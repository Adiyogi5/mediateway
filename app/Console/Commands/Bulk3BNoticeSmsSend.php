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

class Bulk3BNoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-sms-3b-notice';

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
        // Final Appointment Of Arbitrator - 3B - Notice Send
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

        // Join only the 6-type notices to get their details (leftJoin to include cases even when 6-type doesn't exist)
            ->leftJoin('notices as notice6', function ($join) {
                $join->on('notice6.file_case_id', '=', 'file_cases.id')
                    ->where('notice6.notice_type', 6);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_3b');
            })

        // Apply condition for type 6 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 6);
                })->orWhereHas('notices', function ($q) {
                    $q->where('notice_type', 6)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_3b', function ($query) {
                $query->select('notice_3b')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice6.notice as notice6',
                'notice6.sms_status as sms_status6',
                'organization_notice_timelines.notice_3b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchArbitrator = Notice::where('file_case_id', $value->id)->where('notice_type', 6)->first();
              
                if (($assigncaseData->receiveto_casemanager == 1)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();
                    $now = now();

                    $fileCaseId = $value->id;
                    Log::info("Processing Stage 3B Notice - SMS for FileCase ID: {$fileCaseId}");


                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                     if (!empty($value->respondent_mobile)){
                          
                            $mobile     = '91' . preg_replace('/\D/', '', trim($value->respondent_mobile));
                            $smsmessage = "Subject: Appointment of Sole Arbitrator – MediateWay ADR CentreDear Sir/Ma’am,A case has been registered against you under A/c No. {$value->loan_number}.As no objection was received, Mr./Ms. {$arbitratorsData->name} is appointed as Sole Arbitrator as per your Loan Agreement.Proceedings will be conducted online under MediateWay Arbitration Rules.Regards, Team Mediateway";

                        try {
                            $response = Http::post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007824285746901456",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);

                            if ($response->json('isSuccess')) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 6)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                Log::info("Notice 3B SMS sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                Log::warning("Notice 3B SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                Notice::where('file_case_id', $value->id)->where('notice_type', 6)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 3B SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            return false;
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error sending sms for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
