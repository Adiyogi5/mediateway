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

class Bulk5ANoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-whatsapp-5a-notice';

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
                            $inner->Where('whatsapp_notice_status', 0);
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
                'notice10.whatsapp_notice_status as whatsapp_status10',
                'organization_notice_timelines.notice_5a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(2)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();

                $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                $now = now();
                
                $fileCaseId = $value->id;
                Log::info("Processing Stage 4A Notice - Whatsapp for FileCase ID: {$fileCaseId}");

                if (! empty($caseData)) {
                    
                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice10)) {
                        $responseData = [];
                        try {
                            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            // Only remove '91' if it's a country code (i.e., 12 digits and starts with 91)
                            if (strlen($mobileNumber) === 12 && str_starts_with($mobileNumber, '91')) {
                                $mobileNumber = substr($mobileNumber, 2);
                            }

                            $message = "Second Hearing Notice : Loan A/c {$value->loan_number}
Dear Sir/Madam,
You missed the first hearing notice dated [Stage 4-A Notice: Arbitrator 1st  Notice]. Submit your reply & documents on MediateWay portal within 15 days.
Second hearing on [Second Hearing Date], Time AM/PM via Zoom:
Meeting ID: [meeting ID]
Password:  [password]
Link: [direct link to join the meeting]
Failure to appear may lead to ex-parte proceedings.
I confirm my independence.
(Sole Arbitrator)
{$arbitratorsData->name}";

                            $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice10));

                            if (! empty($value->respondent_mobile)) {
                                $response = Http::get(config('services.whatsapp.url'), [
                                    'apikey' => $whatsappApiData['whatsapp_api_key'],
                                    'mobile' => $mobileNumber,
                                    'msg'    => $message,
                                    'pdf'    => $pdfUrl,
                                ]);

                                $responseData = $response->json();

                                if ($response->successful() && isset($responseData['status']) && $responseData['status'] == 1) {
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 10)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status' => 1,
                                        ]);
                                     Log::info("Notice 5A Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                                } else {
                                    $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
                                    $statusCode = $responseData['statuscode'] ?? 'No status code';
                                    Log::warning("Notice 5A Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

                                    Notice::where('file_case_id', $value->id)->where('notice_type', 10)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                        ]);
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 5A Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 5A Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
