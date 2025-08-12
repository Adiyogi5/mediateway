<?php
namespace App\Console\Commands;

use App\Models\CourtroomHearingLink;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateSmsLiveCourtRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-sms-live-court-room';

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
        // ######################################
        // Live Arbitrator Court Room - Send SMS
        // ######################################
        try {

            $links = CourtroomHearingLink::where('sms_status', 0)
                ->limit(3)->get();

            foreach ($links as $link) {

                $fileCase = FileCase::find($link->file_case_id);

                if (! $fileCase) {
                    Log::warning("No FileCase found for link ID: {$link->id}");
                    continue;
                }

                $message = $link->link;
                // Take only the first line before \n
                $message = strtok($message, "\n");
                // Then clean spaces, tabs, etc.
                $message = preg_replace('/\s{5,}/', '    ', $message); // Limit to max 4 spaces
                $message = str_replace(["\n", "\r", "\t"], ' ', $message); // Replace newlines, tabs
                $message = trim($message);

                $smsApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                $mobile     = preg_replace('/\D/', '', trim($fileCase->respondent_mobile));
                $smsmessage = "Dear Sir/Ma’am, Your {$message} at Mediateway is scheduled for Date: {$link->date} at 11:00 AM. The joining link has been sent via email and WhatsApp. Thank you! Team Mediateway.
";

                $response = Http::withHeaders(['apiKey' => $smsApiData['sms_api_key']])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                    "sender"      => "MDTWAY",
                    "peId"        => "1001292642501782120",
                    "teId"        => "1007262404342410482",
                    "message"     => $smsmessage,
                    "smsReciever" => [["reciever" => $mobile]],
                ]);

                if ($response->json('isSuccess')) {
                    $link->update([
                        'sms_status'    => 1,
                        'sms_send_date' => now(),
                    ]);
                    Log::info("Live First Hearing Court Room SMS sent successfully for FileCase ID: {$fileCase->id}");
                } else {
                    Log::warning("Live First Hearing Court Room SMS failed for FileCase ID: {$fileCase->id}. Response: " . $response->body());
                    $link->update(['sms_status' => 2]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error in sending courtroom sms: " . $th->getMessage());
        }
    }

}
