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

class Bulk2BNoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-whatsapp-2b-notice';

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
                            $inner->Where('whatsapp_notice_status', 0);
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
                'notice4.whatsapp_notice_status as whatsapp_notice_status4',
                'organization_notice_timelines.notice_2b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(1)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchCaseManager = Notice::where('file_case_id', $value->id)->where('notice_type', 4)->first();

                if (!empty($assigncaseData->case_manager_id)) {

                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $now                = now();
                    $fileCaseId = $value->id;

                    Log::info("Processing Stage 2B Notice - Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice4)) {
//                         $responseData = [];
//                         try {
//                             $settingdata  = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
//                             $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
//                             $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

//                             // Only remove '91' if it's a country code (i.e., 12 digits and starts with 91)
//                             if (strlen($mobileNumber) === 12 && str_starts_with($mobileNumber, '91')) {
//                                 $mobileNumber = substr($mobileNumber, 2);
//                             }

//                             $message = "Dear {$value->respondent_first_name} {$value->respondent_last_name},
//                             A case has been registered by {$value->claimant_first_name} {$value->claimant_last_name} against you at MediateWay ADR Centre under Clause {$value->arbitration_clause_no} of your loan agreement for online arbitration as per the Arbitration & Conciliation Act, 1996.
// Case Manager:
// Name: {$casemanagerData->name}
// Ph: {$settingdata['phone']} | Email: {$settingdata['email']}
// For details, visit: https://mediateway.com/
// MediateWay ADR Centre";

//                             $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice4));

//                             if (! empty($value->respondent_mobile)) {
//                                 $response = Http::get(config('services.whatsapp.url'), [
//                                     'apikey' => $whatsappApiData['whatsapp_api_key'],
//                                     'mobile' => $mobileNumber,
//                                     'msg'    => $message,
//                                     'pdf'    => $pdfUrl,
//                                 ]);

//                                 $responseData = $response->json();

//                                 if ($response->successful() && isset($responseData['status']) && $responseData['status'] == 1) {
//                                     Notice::where('file_case_id', $value->id)->where('notice_type', 4)
//                                         ->update([
//                                             'whatsapp_dispatch_datetime' => $now,
//                                             'whatsapp_notice_status'     => 1,
//                                         ]);
//                                     Log::info("Notice 2B Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
//                                 } else {
//                                     $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
//                                     $statusCode = $responseData['statuscode'] ?? 'No status code';
//                                     Log::warning("Notice 2B Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

//                                     Notice::where('file_case_id', $value->id)->where('notice_type', 4)
//                                         ->update([
//                                             'whatsapp_notice_status' => 2,
//                                         ]);
//                                 }
//                             }
//                         } catch (\Throwable $th) {
//                             Log::error("Notice 2B Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
//                         }

                        $responseData = [];

                        try {
                            $settingdata = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            // Generate PDF link
                            $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice4));

                            // Build the payload
                            $payload = [
                                "messaging_product" => "whatsapp",
                                "recipient_type" => "individual",
                                "to" => $mobileNumber,
                                "type" => "template",
                                "template" => [
                                    "name" => "stage_2b_notice",
                                    "language" => [
                                        "code" => "en"
                                    ],
                                    "components" => [
                                        [
                                            "type" => "header",
                                            "parameters" => [
                                                [
                                                    "type" => "document",
                                                    "document" => [
                                                        "link" => $pdfUrl
                                                    ]
                                                ]
                                            ]
                                        ],
                                        [
                                            "type" => "body",
                                            "parameters" => [
                                                ["type" => "text", "text" => "{$value->respondent_first_name} {$value->respondent_last_name}"],
                                                ["type" => "text", "text" => "{$value->claimant_first_name} {$value->claimant_last_name}"],
                                                ["type" => "text", "text" => "{$value->arbitration_clause_no}"],
                                                ["type" => "text", "text" => "{$casemanagerData->name}"],
                                                ["type" => "text", "text" => "{$settingdata['phone']}"],
                                                ["type" => "text", "text" => "{$settingdata['email']}"]
                                            ]
                                        ]
                                    ]
                                ]
                            ];
                            
                            // Make the HTTP POST request
                            $response = Http::withHeaders([
                                'Content-Type' => 'application/json',
                                'apikey'       => $whatsappApiData['whatsapp_api_key'],
                            ])->post("https://partnersv1.pinbot.ai/v3/781572805032525/messages", $payload);

                            $responseData = $response->json();

                            if ($response->successful() && isset($responseData['code']) && $responseData['code'] == 200) {
                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'whatsapp_dispatch_datetime' => $now,
                                        'whatsapp_notice_status'     => 1,
                                    ]);
                                Log::info("Notice 2B Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                $errorMsg = $responseData['message'] ?? 'Unknown Error';
                                $statusCode = $responseData['code'] ?? 'No status code';
                                Log::warning("Notice 2B Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

                                Notice::where('file_case_id', $value->id)->where('notice_type', 4)
                                    ->update([
                                        'whatsapp_notice_status' => 2,
                                    ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 2B Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 2B Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
