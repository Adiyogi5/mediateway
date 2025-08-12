<?php
namespace App\Console\Commands;

use App\Models\CourtroomHearingLink;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateEmailLiveCourtRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-email-live-court-room';

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
        // #######################################
        // Live Arbitrator Court Room - Send Email
        // #######################################
        try {
            // Setup SMTP from settings
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

            // Hearing type labels
            $hearingTypeLabels = [
                1 => 'First Hearing Link',
                2 => 'Second Hearing Link',
                3 => 'Final Hearing Link',
            ];

            $links = CourtroomHearingLink::where('email_status', 0)->limit(3)->get();
            
            foreach ($links as $link) {
                $fileCase = FileCase::find($link->file_case_id);

                if (! $fileCase) {
                    Log::warning("No FileCase found for link ID: {$link->id}");
                    continue;
                }

                $email = strtolower(filter_var(trim($fileCase->respondent_email), FILTER_SANITIZE_EMAIL));

                if (empty($email)) {
                    Log::warning("Empty or malformed email for FileCase ID: {$fileCase->id}");
                    $link->update(['email_status' => 2]); // failed
                    continue;
                }

                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email:rfc,dns',
                ]);

                if ($validator->fails()) {
                    Log::warning("Invalid email for FileCase ID {$fileCase->id}: $email");
                    $link->update(['email_status' => 2]);
                    continue;
                }

                $subject     = $hearingTypeLabels[$link->hearing_type] ?? 'Hearing Link';
                $description = $link->link;

                try {
                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($email, $subject) {
                        $message->to($email)->subject($subject);
                    });

                    $link->update([
                        'email_status'    => 1,
                        'email_send_date' => now(),
                    ]);

                    Log::info("Live First Hearing Court Room Email sent successfully for FileCase ID: {$fileCase->id}");
                } catch (\Exception $e) {
                    Log::error("Live First Hearing Court Room Email failed for FileCase ID: {$fileCase->id}, Error: " . $e->getMessage());
                    $link->update(['email_status' => 2]);
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error in sending courtroom emails: " . $th->getMessage());
        }
    }

}
