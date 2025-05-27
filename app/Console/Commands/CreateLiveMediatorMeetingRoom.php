<?php
namespace App\Console\Commands;

use App\Models\Country;
use App\Models\FileCase;
use App\Models\MediatorMeetingRoom;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateLiveMediatorMeetingRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-live-mediator-meeting-room';

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
            // ##############################################
            // Create Live Meeting Room
            // ##############################################

            $sid    = env("TWILIO_ACCOUNT_SID");
            $token  = env("TWILIO_AUTH_TOKEN");
            $sender = env("TWILIO_SENDER");
            $client = new Client($sid, $token);

            $country_data = Country::where('id', 101)->where('status', 1)->first();
            $phone_code   = $country_data->phone_code ?? '';

            $data = Setting::where('setting_type', '3')
                ->get()
                ->pluck('filed_value', 'setting_name')
                ->toArray();

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

            $meetingroomData = MediatorMeetingRoom::where(function ($query) {
                $query->where('date', '>', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('date', now()->toDateString())
                            ->where('time', '>=', now()->toTimeString());
                    });
            })
                ->where('send_mail_to_respondent', 0)
                ->where('send_whatsapp_to_respondent', 0)
                ->get();

           foreach ($meetingroomData as $meetingRoom) {
                $room_id = $meetingRoom->room_id;

                $caseIds = explode(',', $meetingRoom->meeting_room_case_id);
                $cases = FileCase::whereIn('id', $caseIds)->get();

                foreach ($cases as $case) {
                    $email = filter_var($case->respondent_email, FILTER_SANITIZE_EMAIL);

                    $validator = Validator::make(['email' => $email], [
                        'email' => 'required|email:rfc,dns',
                    ]);

                    if ($validator->fails()) {
                        Log::warning("Invalid email address: $email");
                        continue;
                    }

                    $subject = 'Meeting Link';
                    $case_id = $case->id;

                    $messageContent = "Your Meeting at Mediateway is scheduled for Date: {$meetingRoom->date} at {$meetingRoom->time}. Join using this link. Thank you! Mediateway.";
                    $encodedMessage = urlencode($messageContent);
                    $description = route('front.guest.livemediatormeetingroom', ['room_id' => $room_id]) . "?case_id=$case_id&message=$encodedMessage";

                    // Send Email
                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($subject, $email) {
                        $message->to($email)->subject($subject);
                    });

                    if (Mail::failures()) {
                        Log::error("Failed to send email to: $email");
                    }

                    // Send WhatsApp
                    try {
                        $client->messages->create($phone_code . $case->respondent_mobile, [
                            'from' => $sender,
                            'body' => $messageContent,
                        ]);
                    } catch (\Throwable $th) {
                        Log::error('SMS sending failed: ' . $th->getMessage());
                    }
                }

                $meetingRoom->update([
                    'send_mail_to_respondent'     => 1,
                    'email_send_date'             => now(),
                    'send_whatsapp_to_respondent' => 1,
                    'whatsapp_dispatch_datetime'  => now(),
                ]);
            }

        } catch (\Throwable $th) {
            Log::error("Error creating meetingroom entry: " . $th->getMessage());
        }
    }
}
