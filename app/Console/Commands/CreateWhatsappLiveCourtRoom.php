<?php
namespace App\Console\Commands;

use App\Models\CourtroomHearingLink;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateWhatsappLiveCourtRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-whatsapp-live-court-room';

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
        // ##########################################
        // Live Arbitrator Court Room - Send Whatsapp
        // ##########################################
        try {

            $links = CourtroomHearingLink::where('whatsapp_status', 0)
                        ->limit(3)->get();
           
            foreach ($links as $link) {
                $fileCase = FileCase::find($link->file_case_id);

                if (! $fileCase) {
                    Log::warning("No FileCase found for link ID: {$link->id}");
                    continue;
                }

                $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();
                $mobileNumber    = preg_replace('/\D/', '', trim($fileCase->respondent_mobile));

                $message = $link->link;
                $message = preg_replace('/\s{5,}/', '    ', $message); // Limit to max 4 spaces
                $message = str_replace(["\n", "\r", "\t"], ' ', $message); // Replace newlines, tabs
                $message = trim($message); // Remove leading/trailing whitespace
 
                // Build the payload
                $payload = [
                    "messaging_product" => "whatsapp",
                    "recipient_type"    => "individual",
                    "to"                => $mobileNumber,
                    "type"              => "template",
                    "template"          => [
                        "name"       => "first_hearing_link",
                        "language"   => [
                            "code" => "en",
                        ],
                        "components" => [
                            [
                                "type"       => "body",
                                "parameters" => [
                                    ["type" => "text", "text" => "{$message}"],
                                ],
                            ],
                        ],
                    ],
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
                ) {
                    $link->update([
                        'whatsapp_status'    => 1,
                        'whatsapp_send_date' => now(),
                    ]);
                    Log::info("Live First Hearing Court Room Email sent successfully for FileCase ID: {$fileCase->id}");
                } else {
                    $errorMsg   = $responseData['message'] ?? 'Unknown Error';
                    $statusCode = $responseData['code'] ?? 'No status code';
                    Log::error("Live First Hearing Court Room Email failed for FileCase ID: {$fileCase->id}. Reason: $errorMsg (Code: $statusCode)");
                    $link->update(['whatsapp_status' => 2]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error in sending courtroom whatsapp: " . $th->getMessage());
        }
    }

}
