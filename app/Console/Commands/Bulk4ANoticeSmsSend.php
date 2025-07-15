<?php
namespace App\Console\Commands;

use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bulk4ANoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-sms-4a-notice';

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

        // Join only the 9-type notices to get their details (leftJoin to include cases even when 9-type doesn't exist)
            ->leftJoin('notices as notice9', function ($join) {
                $join->on('notice9.file_case_id', '=', 'file_cases.id')
                    ->where('notice9.notice_type', 9);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_4a');
            })

        // Apply condition for type 9 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 9)
                        ->where(function ($inner) {
                            $inner->Where('sms_status', 0);
                        });
                });
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_4a', function ($query) {
                $query->select('notice_4a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice9.notice as notice9',
                'notice9.sms_status as sms_status9',
                'organization_notice_timelines.notice_4a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $now        = now();

                $fileCaseId = $value->id;
                Log::info("Processing Stage 4A Notice - SMS for FileCase ID: {$fileCaseId}");

                // ###############################################################
                // ################ Send SMS using Mobile Number #################
                if (! empty($value->respondent_mobile)) {

                    $first_hearingDate = $value->first_hearing_date;

                    $mobile = '91' . preg_replace('/\D/', '', trim($value->respondent_mobile));

                    $smsmessage = "Ref: Arbitration Hearing – A/c {$value->loan_number}Dear Sir/Ma’am,Your first virtual hearing is on {$first_hearingDate} at 11.30 AM Digital Room details have been sent to your registered email and WhatsApp.Submit your reply/documents via the MediateWay portal before the hearing.Non-appearance may result in ex-parte proceedings (Sec. 25, A&C Act). (Sole Arbitrator)";

                    try {
                        $response = Http::post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                            "sender"      => "MDTWAY",
                            "peId"        => "1001292642501782120",
                            "teId"        => "1007875445521607304",
                            "message"     => $smsmessage,
                            "smsReciever" => [["reciever" => $mobile]],
                        ]);

                        if ($response->json('isSuccess')) {
                            Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                ->update([
                                    'sms_send_date' => $now,
                                    'sms_status'    => 1,
                                ]);
                            Log::info("Notice 4A SMS sent successfully for FileCase ID: {$fileCaseId}");
                        } else {
                            Log::warning("Notice 4A SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                            Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                ->update([
                                    'sms_status' => 2,
                                ]);
                        }
                    } catch (\Throwable $th) {
                        Log::error("Notice 4A SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        return false;
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 4A SMS FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
