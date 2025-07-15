<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\ConciliationNotice;
use App\Models\FileCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConciliationNoticeSmsSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:conciliation-notice-sms-send';

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
        // Conciliation Notice Send Via SMS - By Case Manager
        // ######################################################
        $caseData = FileCase::with('file_case_details')
            ->leftJoin('conciliation_notices', 'conciliation_notices.file_case_id', '=', 'file_cases.id')
            ->where('conciliation_notices.conciliation_notice_type', 2)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_mobile')
                    ->where('file_cases.respondent_mobile', '!=', '');
            })
            ->where('conciliation_notices.sms_status', 0)
            ->select(
                'file_cases.*',
                'conciliation_notices.file_case_id',
                'conciliation_notices.conciliation_notice_type',
                'conciliation_notices.notice_copy',
                'conciliation_notices.email_status',
            )
            ->limit(4)
            ->get();

        foreach ($caseData as $key => $value) {
            try {

                $now    = now();
                $fileCaseId = $value->id;

                Log::info("Conciliation Processing For Meeting - SMS for FileCase ID: {$fileCaseId}");

                    // ###############################################################
                    // ################ Send SMS using Mobile Number #################
                    if ($value->respondent_mobile) {
                    
                        // $mobile     = '91' . preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $mobile     = preg_replace('/\D/', '', trim($value->respondent_mobile));
                        $smsmessage = "Sub.: Invitation for Online Conciliation
Dear Sir/Ma’am,
As per Section 62 of the Arbitration and Conciliation Act, 1996, you are invited to participate in an Online Conciliation Meeting regarding the dispute with {$value->claimant_first_name} {$value->claimant_last_name} concerning your CC / Loan Account No. {$value->loan_number}.
The Conciliation will be conducted via the MediateWay Online Platform. All relevant details—including the date, time, meeting link, and name of the Conciliator—have been shared with you on your registered WhatsApp number and email address for your convenience.
This is your final opportunity to settle the matter amicably before legal action is initiated. Your cooperation is requested to resolve the dispute in a fair and efficient manner.
For any queries or support, you may reach us via the contact details mentioned below.
We look forward to your participation.
MediateWay ADR Centre
Contact Information: [ 9461165841/mediatewayinfo@gmail.com]";
                      
                        try {
                            $response = Http::withHeaders(['apiKey' => 'aHykmbPNHOE9KGE',])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007902562577448754",
                                "message"     => $smsmessage,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);
 
                            if ($response->json('isSuccess')) {
                                ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 2)
                                    ->update([
                                        'sms_send_date' => $now,
                                        'sms_status'    => 1,
                                    ]);
                                    Log::info("Conciliation SMS sent successfully for FileCase ID: {$fileCaseId}");
                            } else {
                                Log::warning("Conciliation SMS failed for FileCase ID: {$fileCaseId}. Response: " . $response->body());
                                ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 2)
                                    ->update([
                                        'sms_status' => 2,
                                    ]);
                            }
                        } catch (\Throwable $th) {
                            Log::error("Conciliation SMS API exception for FileCase ID: {$fileCaseId}. Error: " . $th->getMessage());
                            return false;
                        }
                        
                    }

            } catch (\Throwable $th) {
                Log::error("Error processing Conciliation SMS FileCase ID: {$value->id}. Exception: " . $th->getMessage());
            }
        }
    }
}
