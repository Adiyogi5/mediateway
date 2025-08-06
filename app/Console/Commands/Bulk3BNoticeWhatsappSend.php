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

class Bulk3BNoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-whatsapp-3b-notice';

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
                $query->WhereHas('notices', function ($q) {
                    $q->where('notice_type', 6)
                        ->where(function ($inner) {
                            $inner->where('whatsapp_notice_status', 0);
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
                'notice6.whatsapp_notice_status as whatsapp_status6',
                'organization_notice_timelines.notice_3b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(2)
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

                    Log::info("Stage 3B Notice - Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice6)) {
                        $responseData = [];
                        try {
                            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            // Only remove '91' if it's a country code (i.e., 12 digits and starts with 91)
                            if (strlen($mobileNumber) === 12 && str_starts_with($mobileNumber, '91')) {
                                $mobileNumber = substr($mobileNumber, 2);
                            }

                            $message = "Subject: Appointment of Arbitrator : MediateWay ADR Centre
Dear {$value->respondent_first_name} {$value->respondent_last_name},
A case has been registered by {$value->claimant_first_name} {$value->claimant_last_name} against you for online arbitration under Loan A/c No. {$value->loan_number}.
Following our prior proposal and no objections received, Mr./Ms. {$arbitratorsData->name}, is hereby appointed as Sole Arbitrator under the terms of your Loan Agreement.
Arbitration will proceed online via MediateWay ADR Centre.
This serves as official notice under MediateWay Arbitration Rules.
Regards,
MediateWay ADR Centre";

                            $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice6));
                            $pdfPath = public_path(str_replace(url('/'), '', $pdfUrl)); // converts URL to file path
                            $pdfName = basename($pdfPath);

                            if (! empty($value->respondent_mobile)) {
                                // $response = Http::get(config('services.whatsapp.url'), [
                                //     'apikey' => $whatsappApiData['whatsapp_api_key'],
                                //     'mobile' => $mobileNumber,
                                //     'msg'    => $message,
                                //     'pdf'    => $pdfUrl,
                                // ]);
                                $response = Http::withHeaders([
                                    'Authorization' => 'Bearer 8348c5b0-7123-11f0-98fc-02c8a5e042bd',
                                    'Accept'        => 'application/json',
                                ])
                                ->attach('attachment', file_get_contents($pdfPath), $pdfName)
                                ->post('https://consolev1.pinbot.ai/api/send', [
                                    'mobile' => '91' . $mobileNumber, // adding back +91
                                    'message' => $message,
                                    'type' => 'pdf', // or omit if not required
                                ]);

                                $responseData = $response->json();

                                if ($response->successful() && isset($responseData['status']) && $responseData['status'] == 1) {
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 6)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status' => 1,
                                        ]);
                                    Log::info("Notice 3B Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                                } else {
                                    $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
                                    $statusCode = $responseData['statuscode'] ?? 'No status code';
                                    Log::warning("Notice 3B Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

                                    Notice::where('file_case_id', $value->id)->where('notice_type', 6)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                        ]);
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error("Notice 3B Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error processing Notice 3B Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
