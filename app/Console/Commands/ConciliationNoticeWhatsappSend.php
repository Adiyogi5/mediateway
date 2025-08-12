<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\ConciliationNotice;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConciliationNoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:conciliation-notice-whatsapp-send';

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
        // ###########################################################
        // Conciliation Notice Send Via Whatsapp - By Case Manager
        // ###########################################################
        $caseData = FileCase::with('file_case_details')
            ->leftJoin('conciliation_notices', 'conciliation_notices.file_case_id', '=', 'file_cases.id')
            ->where('conciliation_notices.conciliation_notice_type', 2)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_mobile')
                    ->where('file_cases.respondent_mobile', '!=', '');
            })
            ->whereNotNull('conciliation_notices.notice_copy')
            ->where('conciliation_notices.whatsapp_notice_status', 0)
            ->select(
                'file_cases.*',
                'conciliation_notices.file_case_id',
                'conciliation_notices.conciliation_notice_type',
                'conciliation_notices.notice_copy',
                'conciliation_notices.email_status',
            )
            ->limit(3)
            ->get();
        
        foreach ($caseData as $key => $value) {
            try {

                $now    = now();
                $fileCaseId = $value->id;

                Log::info("Processing Conciliation Notice For Meeting - Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice_copy)) {
                        $responseData = [];
                        try {
                            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            // Generate PDF link
                            $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice_copy));

                            // Build the payload
                            $payload = [
                                "messaging_product" => "whatsapp",
                                "recipient_type" => "individual",
                                "to" => $mobileNumber,
                                "type" => "template",
                                "template" => [
                                    "name" => "conciliation_notices",
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
                                                ["type" => "text", "text" => "{$value->claimant_first_name} {$value->claimant_last_name}"],
                                                ["type" => "text", "text" => "{$value->loan_number}"]
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
                         
                            if (
                                $response->successful() &&
                                isset($responseData['messages'][0]['message_status']) &&
                                $responseData['messages'][0]['message_status'] === 'accepted'
                            ){
                                    ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 2)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status'     => 1,
                                        ]);
                                    Log::info("Conciliation Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                 $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
                                    $statusCode = $responseData['statuscode'] ?? 'No status code';
                                    Log::warning("Conciliation Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

                                    ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 2)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                            'whatsapp_bounce_datetime' => $now,
                                        ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Conciliation Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                        }
                    }
            } catch (\Throwable $th) {
                Log::error("Error Processing Conciliation Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
