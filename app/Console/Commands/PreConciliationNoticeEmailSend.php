<?php
namespace App\Console\Commands;

use App\Models\ConciliationNotice;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PreConciliationNoticeEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:preconciliation-notice-email-send';

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
        // ########################################################
        // Pre-Conciliation Notice Send Via Email - By Case Manager
        // ########################################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->leftJoin('conciliation_notices', 'conciliation_notices.file_case_id', '=', 'file_cases.id')
            ->where('conciliation_notices.conciliation_notice_type', 1)
            ->where(function ($query) {
                $query->whereNotNull('file_cases.respondent_email')
                    ->where('file_cases.respondent_email', '!=', '');
            })
            ->whereNotNull('conciliation_notices.notice_copy')
            ->where('conciliation_notices.email_status', 0)
            ->select(
                'file_cases.*',
                'conciliation_notices.file_case_id',
                'conciliation_notices.conciliation_notice_type',
                'conciliation_notices.notice_copy',
                'conciliation_notices.email_status'
            )
            ->limit(4)
            ->get();
      
        foreach ($caseData as $key => $value) {
            try {
                $now                           = now();
                
                $fileCaseId = $value->id;
                Log::info("Processing Pre-Conciliation Email for FileCase ID: {$fileCaseId}");

                // #########################################################
                // ################# Send Email using SMTP #################
                if (!empty($value->notice_copy)) {
                    
                    //Send Email with Notice for Assign Arbitrator
                    $data = Setting::where('setting_type', '3')->get()->pluck('filed_value', 'setting_name')->toArray();

                    Config::set("mail.mailers.smtp", [
                        'transport'  => 'smtp',
                        'host'       => $data['smtp_host'],
                        'port'       => $data['smtp_port'],
                        'encryption' => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                        'username'   => $data['smtp_user'],
                        'password'   => $data['smtp_pass'],
                        'timeout'    => null,
                        'auth_mode'  => null,
                    ]);

                    Config::set("mail.from", [
                        'address' => $data['email_from'],
                        'name'    => config('app.name'),
                    ]);

                    // ###################################################################
                    // ################# Send Email using Email Address ##################
                    if (! empty($value->respondent_email)) {

                        $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                        $validator = Validator::make(['email' => $email], [
                            'email' => 'required|email:rfc,dns',
                        ]);

                        if ($validator->fails()) {
                            Log::warning("Invalid email address: $email");
                            ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)
                                ->update([
                                    'email_status' => 2,
                                    'email_bounce_datetime' => $now,
                                ]);
                        } else {

                            $subject     = "Subject: Service of Legal Notice--- {$value->loan_number} (Co-branded with Bajaj Finserv)";
                            $description = "Dear {$value->respondent_first_name} {$value->respondent_last_name},

Please find attached a RECALL NOTICE/ DEMAND NOTICE  addressed to you on behalf of our client, RBL Bank Ltd.

You are requested to peruse the same carefully and take appropriate steps as advised therein.

This email and the attached legal notice are being sent without prejudice to our client’s rights and remedies available in law, all of which are expressly reserved.

Attachment: Legal Notice.pdf
Regards,

Anil  Kumar  Sharma  And  Associates

Advocates And Legal Consultants
LITIGATION | ADVISORY | COMPLIANCE
(M) +91-9414295841/7852891583
EMAIL: advocatejdr@gmail.com
Services Provided by MediateWay ADR Centre LLP, Online Platform";

                            try {
                                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                    $message->to($email)
                                        ->cc('legaldesk@rblbank.com')
                                        ->subject($subject)
                                        ->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice_copy)), [
                                            'mime' => 'application/pdf',
                                        ]);
                                });

                                ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)
                                    ->update([
                                        'notice_send_date' => $now,
                                        'email_status'     => 1,
                                    ]);
                                Log::info("Pre-Conciliation Email sent successfully for FileCase ID: {$fileCaseId}");
                            } catch (\Exception $e) {
                                Log::warning("Pre-Conciliation Email failed for FileCase ID: {$fileCaseId}. Response: " . $e->getMessage());
                                ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)
                                    ->update([
                                        'email_status' => 2,
                                        'email_bounce_datetime' => $now,
                                    ]);
                            }
                        }
                    }
                }

            } catch (\Throwable $th) {
                Log::error("Error sending Pre-Conciliation email for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
