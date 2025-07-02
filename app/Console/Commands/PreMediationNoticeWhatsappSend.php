<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\MediationNotice;
use App\Models\FileCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PreMediationNoticeWhatsappSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:premediation-notice-whatsapp-send';

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
        // Pre-Mediation Notice Send Via Whatsapp - By Case Manager
        // ###########################################################
        $caseData = FileCase::with('file_case_details')
            ->leftJoin('mediation_notices', 'mediation_notices.file_case_id', '=', 'file_cases.id')
            ->where('mediation_notices.mediation_notice_type', 1)
            ->whereNotNull('file_cases.respondent_mobile')
            ->whereNotNull('mediation_notices.notice_copy')
            ->where('mediation_notices.whatsapp_notice_status', 0)
            ->select(
                'file_cases.*',
                'mediation_notices.file_case_id',
                'mediation_notices.mediation_notice_type',
                'mediation_notices.notice_copy',
                'mediation_notices.email_status',
            )
            ->limit(10)
            ->get();
        
        foreach ($caseData as $key => $value) {
            try {

                $now    = now();
                $fileCaseId = $value->id;

                Log::info("Processing Mediation Whatsapp for FileCase ID: {$fileCaseId}");

                    // ###################################################################
                    // ############ Send Whatsapp Message using Mobile Number ############
                    if (!empty($value->notice_copy)) {
                        try {
                            $mobileNumber = preg_replace('/\D/', '', trim($value->respondent_mobile));

                            $message = "Dear {$value->respondent_first_name} {$value->respondent_last_name},
[ {$value->loan_number} (Co-branded with Bajaj Finserv)]

Please be informed that a RECALL NOTICE/ DEMAND NOTICE dated 24-06-2025 has been issued to you on behalf of our client, RBL Bank Ltd., concerning non-payment of dues against your Credit Card account.

A copy of the said notice is being sent to you via this WhatsApp message for your urgent attention and necessary action.

Kindly treat this matter with priority. This communication is issued without prejudice to any legal rights and remedies available to our client, all of which are expressly reserved.

Regards,

Anil  Kumar  Sharma  And  Associates

Advocates And Legal Consultants
LITIGATION | ADVISORY | COMPLIANCE
(M) +91-9414295841/7852891583
EMAIL: advocatejdr@gmail.com
Services Provided by MediateWay ADR Centre LLP, Online Platform";

                            $pdfUrl = url(str_replace('\\', '/', 'public/storage/' . $value->notice_copy));

                            if (! empty($value->respondent_mobile)) {
                                $response = Http::get(config('services.whatsapp.url'), [
                                    'apikey' => config('services.whatsapp.api_key'),
                                    'mobile' => $mobileNumber,
                                    'msg'    => $message,
                                    'pdf'    => $pdfUrl,
                                ]);

                                if ($response->successful()) {
                                    MediationNotice::where('file_case_id', $value->id)
                                        ->update([
                                            'whatsapp_dispatch_datetime' => $now,
                                            'whatsapp_notice_status'     => 1,
                                        ]);
                                        Log::info("Mediation Whatsapp sent successfully for FileCase ID: {$fileCaseId}");
                                    return true;
                                } else {
                                    Log::warning("Mediation Whatsapp failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                    MediationNotice::where('file_case_id', $value->id)
                                        ->update([
                                            'whatsapp_notice_status' => 2,
                                        ]);
                                    return false;
                                }
                            }
                        } catch (\Throwable $th) {
                            Log::error("Mediation Whatsapp API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            // $notice->update(['whatsapp_notice_status' => 2]);
                        }
                    }

            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error processing Mediation Whatsapp FileCase ID: {$value->id}. Exception: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
