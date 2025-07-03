<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\MediationNotice;
use App\Models\FileCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PreMediationNoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:premediation-notice-sms-send';

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
        // ######################################################
        // Pre-Mediation Notice Send Via SMS - By Case Manager
        // ######################################################
        $caseData = FileCase::with('file_case_details')
            ->leftJoin('mediation_notices', 'mediation_notices.file_case_id', '=', 'file_cases.id')
            ->where('mediation_notices.mediation_notice_type', 1)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_mobile')
                    ->where('file_cases.respondent_mobile', '!=', '');
            })
            ->where('mediation_notices.sms_status', 0)
            ->select(
                'file_cases.*',
                'mediation_notices.file_case_id',
                'mediation_notices.mediation_notice_type',
                'mediation_notices.notice_copy',
                'mediation_notices.email_status',
            )
            ->limit(5)
            ->get();

        foreach ($caseData as $key => $value) {
            try {

                $now    = now();
                $fileCaseId = $value->id;

                Log::info("Mediation Processing SMS for FileCase ID: {$fileCaseId}");

                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                    if ($value->respondent_mobile) {
                    
                        // $mobile     = '91' . preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $mobile     = preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $smsmessage = "SUB: Recall/Demand Notice – Credit Card No. {$value->loan_number} (Co-branded with Bajaj Finserv)
DearSir/Maam,
Rs {$value->file_case_details->foreclosure_amount} is overdue on your RBL Bajaj Finserv Credit Card. Despite reminders, payment is still pending. Non-payment may lead to CIBIL reporting, legal action under BNS, and recovery steps including informing your employer. A copy of the notice has also been sent to your registered WhatsApp & Email ID for your urgent attention. Ignore if already paid.
Anil Kumar Sharma, Advocate On Behalf of RBL BANK LTD.
Team MediateWay.";
                      
                        try {
                            $response = Http::withHeaders(['apiKey' => 'aHykmbPNHOE9KGE',])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007501649871179908",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);
 
                            if ($response->json('isSuccess')) {
                                MediationNotice::where('file_case_id', $value->id)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                    Log::info("Mediation SMS sent successfully for FileCase ID: {$fileCaseId}");
                                return true;
                            } else {
                                Log::warning("Mediation SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                MediationNotice::where('file_case_id', $value->id)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                                return false;
                            }
                        } catch (\Throwable $th) {
                            Log::error("Mediation SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            return false;
                        }
                        
                    }

            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error processing Mediation SMS FileCase ID: {$value->id}. Exception: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
