<?php
namespace App\Console\Commands;

use App\Models\FileCase;
use App\Models\MediatorMeetingRoom;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateLiveWhatsappMediatorMeetingRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-live-whatsapp-mediator-meeting-room';

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
        try {
            // ##################################################
            // Create Live Mediation Meeting Room - Send Whatsapp
            // ##################################################

            $whatsappApiData = Setting::where('setting_type', '5')->get()->pluck('filed_value', 'setting_name')->toArray();

            $meetingroomData = MediatorMeetingRoom::where(function ($query) {
                $query->where('date', '>', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('date', now()->toDateString())
                            ->where('time', '>=', now()->toTimeString());
                    });
            })
                ->where('send_whatsapp_to_respondent', 0)
                ->get();

            foreach ($meetingroomData as $meetingRoom) {
                $room_id = $meetingRoom->room_id;

                $caseIds = explode(',', $meetingRoom->meeting_room_case_id);
                $cases   = FileCase::whereIn('id', $caseIds)->get();

                $allWhatsappSent = true; // Flag to track if all messages succeed

                foreach ($cases as $case) {
                    $mobileNumber = preg_replace('/\D/', '', trim($case->respondent_mobile));

                    // Clean mobile number: Remove '91' if it's a leading country code
                    if (strlen($mobileNumber) === 12 && str_starts_with($mobileNumber, '91')) {
                        $mobileNumber = substr($mobileNumber, 2);
                    }

                    $case_id = $case->id;

                    $messageContent = "Your Meeting at Mediateway is scheduled for Date: {$meetingRoom->date} at {$meetingRoom->time}. Join using this link. Thank you! Mediateway.";
                    $encodedMessage = urlencode($messageContent);
                    $message = route('front.guest.livemediatormeetingroom', ['room_id' => $room_id]) . "?case_id=$case_id&message=$encodedMessage";

                    try {
                        $response = Http::get(config('services.whatsapp.url'), [
                            'apikey' => $whatsappApiData['whatsapp_api_key'],
                            'mobile' => $mobileNumber,
                            'msg'    => $message,
                        ]);

                        $responseData = $response->json();

                        if ($response->successful() && isset($responseData['status']) && $responseData['status'] == 1) {
                            Log::info("Mediation Meeting Room WhatsApp sent successfully for FileCase ID: {$case_id}");
                        } else {
                            $errorMsg = $responseData['errormsg'] ?? 'Unknown Error';
                            $statusCode = $responseData['statuscode'] ?? 'No status code';
                            Log::warning("WhatsApp failed for FileCase ID: {$case_id}. Reason: $errorMsg (Code: $statusCode)");
                            $allWhatsappSent = false;
                        }
                    } catch (\Exception $e) {
                        Log::error("WhatsApp exception for FileCase ID: {$case_id}. Error: " . $e->getMessage());
                        $allWhatsappSent = false;
                    }
                }

                // ✅ Update only if all WhatsApp messages for this room succeeded
                if ($allWhatsappSent) {
                    $meetingRoom->update([
                        'send_whatsapp_to_respondent'   => 1,
                        'whatsapp_dispatch_datetime'    => now(),
                    ]);
                } else {
                    $meetingRoom->update([
                        'send_whatsapp_to_respondent' => 2,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error creating mediation meetingroom entry: " . $th->getMessage());
        }
    }
}
