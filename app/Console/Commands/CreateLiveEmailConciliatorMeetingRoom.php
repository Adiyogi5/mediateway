<?php
namespace App\Console\Commands;

use App\Models\ConciliatorMeetingRoom;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateLiveEmailConciliatorMeetingRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-live-email-conciliator-meeting-room';

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
            // Create Live Conciliation Meeting Room - Send Email
            // ##################################################

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

            $meetingroomData = ConciliatorMeetingRoom::where(function ($query) {
                $query->where('date', '>', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('date', now()->toDateString())
                            ->where('time', '>=', now()->toTimeString());
                    });
            })
                ->where('send_mail_to_respondent', 0)
                ->get();

            foreach ($meetingroomData as $meetingRoom) {
                $room_id = $meetingRoom->room_id;

                $caseIds = explode(',', $meetingRoom->meeting_room_case_id);
                $cases   = FileCase::whereIn('id', $caseIds)->get();

                $allEmailsSent = true; // Flag to track if all emails succeed

                foreach ($cases as $case) {
                    $email = strtolower(filter_var(trim($case->respondent_email), FILTER_SANITIZE_EMAIL));

                    if (empty($email)) {
                        Log::warning("Empty or malformed email for FileCase ID: {$case->id}");
                        $allEmailsSent = false;
                        continue;
                    }

                    $validator = Validator::make(['email' => $email], [
                        'email' => 'required|email:rfc,dns',
                    ]);

                    if ($validator->fails()) {
                        Log::warning("Invalid email address for FileCase ID {$case->id}: $email");
                        $allEmailsSent = false;
                        continue;
                    }

                    $subject = 'Conciliation Meeting Link';
                    $case_id = $case->id;

                    $messageContent = "Your Meeting at Mediateway is scheduled for Date: {$meetingRoom->date} at {$meetingRoom->time}. Join using this link. Thank you! Mediateway.";
                    $encodedMessage = urlencode($messageContent);
                    $description    = route('front.guest.liveconciliatormeetingroom', ['room_id' => $room_id]) . "?case_id=$case_id&message=$encodedMessage";

                    try {
                        Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($subject, $email) {
                            $message->to($email)->subject($subject);
                        });

                        Log::info("Conciliation Meeting Room Email sent successfully for FileCase ID: {$case_id}");
                    } catch (\Exception $e) {
                        Log::warning("Conciliation Meeting Room Email failed for FileCase ID: {$case_id}. Error: " . $e->getMessage());
                        $allEmailsSent = false;
                    }
                }

                // ✅ Update only if all emails succeeded
                if ($allEmailsSent) {
                    $meetingRoom->update([
                        'send_mail_to_respondent' => 1,
                        'email_send_date'         => now(),
                    ]);
                } else {
                    // Optional: log or flag if partial or complete failure
                    $meetingRoom->update([
                        'send_mail_to_respondent' => 2,
                    ]);
                }
            }

        } catch (\Throwable $th) {
            Log::error("Error creating meetingroom entry: " . $th->getMessage());
        }
    }
}
