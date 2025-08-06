<?php
namespace App\Console\Commands;

use App\Models\AssignCase;
use App\Models\FileCase;
use App\Models\Notice;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bulk2BNoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-sms-2b-notice';

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

        // Join only the 4-type notices to get their details (leftJoin to include cases even when 4-type doesn't exist)
            ->leftJoin('notices as notice4', function ($join) {
                $join->on('notice4.file_case_id', '=', 'file_cases.id')
                    ->where('notice4.notice_type', 4);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_2b');
            })

        // Apply condition for type 4 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 4)
                        ->where(function ($inner) {
                            $inner->Where('sms_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_2b', function ($query) {
                $query->select('notice_2b')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice4.notice as notice4',
                'notice4.sms_status as sms_status4',
                'organization_notice_timelines.notice_2b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchCaseManager = Notice::where('file_case_id', $value->id)->where('notice_type', 4)->first();

                if (! empty($assigncaseData->case_manager_id)) {
                    $now                = now();

                    $fileCaseId = $value->id;
                    Log::info("Processing Stage 2B Notice - SMS for FileCase ID: {$fileCaseId}");

                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                    if (!empty($value->respondent_mobile)) {
                        $smsApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                        $mobile        = preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $smsmessage = "Subject: Intimation of Case Registration – MediateWay ADR Centre Dear {$value->respondent_first_name}, A case has been registered by {$value->claimant_first_name} against you at MediateWay ADR Centre under Clause of your loan agreement/credit card facility form for online arbitration as per the A & C Act, 1996. For details, visit: https://mediateway.com/ MediateWay ADR Centre
";

                        try {
                            $response = Http::withHeaders(['apiKey' => $smsApiData['sms_api_key'],])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007612740524993540",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);

                            if ($response->json('isSuccess')) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                Log::info("Notice 2B SMS sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                Log::warning("Notice 2B SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 2B SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            return false;
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 2B SMS FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
