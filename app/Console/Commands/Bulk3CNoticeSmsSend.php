<?php
namespace App\Console\Commands;

use App\Models\AssignCase;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\Notice;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bulk3CNoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-sms-3c-notice';

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
        // Arbitrator accept the Case - 3C - Notice Send
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

        // Join only the 7-type notices to get their details (leftJoin to include cases even when 7-type doesn't exist)
            ->leftJoin('notices as notice7', function ($join) {
                $join->on('notice7.file_case_id', '=', 'file_cases.id')
                    ->where('notice7.notice_type', 7);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_3c');
            })

        // Apply condition for type 7 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 7)
                        ->where(function ($inner) {
                            $inner->Where('sms_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_3c', function ($query) {
                $query->select('notice_3c')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice7.notice as notice7',
                'notice7.sms_status as sms_status7',
                'organization_notice_timelines.notice_3c',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(3)
            ->get();


        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchArbitrator = Notice::where('file_case_id', $value->id)->where('notice_type', 7)->first();
              
                if (!empty($assigncaseData)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                    $now                = now();

                    $fileCaseId = $value->id;
                    Log::info("Processing Stage 3C Notice - Email for FileCase ID: {$fileCaseId}");

                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                    if (!empty($arbitratorsData->mobile)) {
                        $smsApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                        $mobile     = preg_replace('/\D/', '', trim($arbitratorsData->mobile));
                        $smsmessage = "Subject: Arbitrator’s Acceptance & Disclosure Dear Sir/Madam, I am appointed Sole Arbitrator by MediateWay ADR Centre in the dispute under Loan A/c No. {$value->loan_number} between {$value->claimant_first_name} and you. I accept the role and confirm my independence under the A&C Act, 1996. Proceedings will be online, in English, as per MediateWay Rules. Non-participation may lead to ex-parte or case closure. (Sole Arbitrator)
";

                        try {
                            $response = Http::withHeaders(['apiKey' => $smsApiData['sms_api_key'],])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007770766842477944",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);

                            if ($response->json('isSuccess')) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 7)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                Log::info("Notice 3C SMS sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                Log::warning("Notice 3C SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                Notice::where('file_case_id', $value->id)->where('notice_type', 7)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 3C SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            return false;
                        }
                        
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 3C SMS FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
