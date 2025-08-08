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

class PreConciliationNoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:preconciliation-notice-whatsapp-send';

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
        // Pre-Conciliation Notice Send Via Whatsapp - By Case Manager
        // ###########################################################
        $caseData = FileCase::with('file_case_details')
            ->leftJoin('conciliation_notices', 'conciliation_notices.file_case_id', '=', 'file_cases.id')
            ->where('conciliation_notices.conciliation_notice_type', 1)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_mobile')
                    ->where('file_cases.respondent_mobile', '!=', '');
            })
            ->whereNotNull('conciliation_notices.notice_copy')
            ->where('conciliation_notices.whatsapp_notice_status', 0)
            ->whereNull('conciliation_notices.deleted_at')
            ->select(
                'file_cases.*',
                'conciliation_notices.file_case_id',
                'conciliation_notices.conciliation_notice_type',
                'conciliation_notices.notice_copy',
                'conciliation_notices.email_status',
                'conciliation_notices.whatsapp_notice_status',
                'conciliation_notices.sms_status',
            )
            ->limit(2)
            ->get();
        
        foreach ($caseData as $key => $value) {
            try {

                $now    = now();
                $fileCaseId = $value->id;

                Log::info("Processing Pre-Conciliation Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice_copy)) {
                        $responseData = [];
                        try {
                            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            // Only remove '91' if it's a country code (i.e., 12 digits and starts with 91)
                            if (strlen($mobileNumber) === 12 && str_starts_with($mobileNumber, '91')) {
                                $mobileNumber = substr($mobileNumber, 2);
                            }

                            $message = "Dear {$value->respondent_first_name} {$value->respondent_last_name},
(Account No. {$value->loan_number})
Please be informed that a Legal Notice has been issued to you on behalf of our client, RBL Bank Ltd., concerning non-payment of dues against your CC/Loan Account.
A copy of the said notice is being sent to you via this WhatsApp message for your urgent attention and necessary action.
Kindly treat this matter with priority. This communication is issued without prejudice to any legal rights and remedies available to our client, all of which are expressly reserved.
Regards,
Anil  Kumar  Sharma  And  Associates
Advocates And Legal Consultants
LITIGATION | ADVISORY | COMPLIANCE
(M) +91-9414295841/7852891583
EMAIL: advocatejdr@gmail.com
WhatsApp Services Provided by MediateWay ADR Centre LLP, Online Platform.";

                            $pdfUrl = url(str_replace('\\', '/', 'storage/' . $value->notice_copy));

                            if (! empty($value->respondent_mobile)) {
                                $response = Http::get(config('services.whatsapp.url'), [
                                    'apikey' => $whatsappApiData['whatsapp_api_key'],
                                    'mobile' => $mobileNumber,
                                    'msg'    => $message,
                                    'pdf'    => $pdfUrl,
                                ]);

                                $responseData = $response->json();

                                if ($response->successful() && isset($responseData['status']) && $responseData['status'] == 1) {
                                    ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status'     => 1,
                                        ]);
                                        Log::info("Pre-Conciliation Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                                } else {
                                    $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
                                    $statusCode = $responseData['statuscode'] ?? 'No status code';
                                    Log::warning("Pre-Conciliation Whatsapp failed for FileCase ID: {$fileCaseId}. Reason: $errorMsg (Code: $statusCode)");

                                    ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                            'whatsapp_bounce_datetime' => $now,
                                        ]);
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error("Pre-Conciliation Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            // $notice->update(['whatsapp_notice_status' => 2]);
                        }
                    }

            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error processing Pre-Conciliation Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
