<?php
namespace App\Console\Commands;

use App\Models\CourtRoom;
use App\Models\FileCase;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateLiveCourtRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-live-court-room';

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
        // ##############################################
        // Create Live Arbitrator Court Room - Send Email
        // ##############################################

        try {
            // Handle the three different hearing types
            $hearingTypes = [
                1 => 'first_hearing_date',
                2 => 'second_hearing_date',
                3 => 'final_hearing_date',
            ];

            // Fetch SMTP settings only once
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

            // Define hearing types for email subjects
            $hearingTypeLabels = [
                1 => 'First Hearing Link',
                2 => 'Second Hearing Link',
                3 => 'Final Hearing Link',
            ];

            foreach ($hearingTypes as $hearingType => $hearingDateColumn) {
                $fileCases = FileCase::with(['assignedCases' => function ($query) {
                    $query->select('case_id', 'arbitrator_id', 'case_manager_id', 'advocate_id');
                }])
                    ->whereNotNull($hearingDateColumn)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->get()
                    ->groupBy($hearingDateColumn);

                foreach ($fileCases as $date => $cases) {
                    $courtroomData = CourtRoom::where('date', $date)
                        ->where('hearing_type', $hearingType)
                        ->first();

                    if (! $courtroomData) {
                        $individual_ids      = $cases->pluck('individual_id')->filter()->unique()->implode(',');
                        $organization_ids    = $cases->pluck('organization_id')->filter()->unique()->implode(',');
                        $court_room_case_ids = $cases->pluck('id')->unique()->implode(',');

                        $arbitrator_id   = optional($cases->first()->assignedCases->first())->arbitrator_id;
                        $case_manager_id = optional($cases->first()->assignedCases->first())->case_manager_id;
                        $advocate_id     = optional($cases->first()->assignedCases->first())->advocate_id;

                        $prefix     = $individual_ids ? 'INDI' : 'ORG';
                        $lastRoom   = CourtRoom::where('room_id', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
                        $nextNumber = $lastRoom ? ((int) str_replace($prefix . '-', '', $lastRoom->room_id) + 1) : 1;
                        $room_id    = $prefix . '-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

                        $courtRoom = CourtRoom::create([
                            'room_id'            => $room_id,
                            'court_room_case_id' => $court_room_case_ids,
                            'hearing_type'       => $hearingType,
                            'individual_id'      => $individual_ids ?? null,
                            'organization_id'    => $organization_ids ?? null,
                            'arbitrator_id'      => $arbitrator_id,
                            'case_manager_id'    => $case_manager_id,
                            'advocate_id'        => $advocate_id,
                            'date'               => $date,
                            'time'               => '11:00:00',
                            'status'             => 0,
                        ]);

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

                            $subject = $hearingTypeLabels[$hearingType] ?? 'Hearing Link';
                            $case_id = $case->id;

                            $messageContent = "Your $subject at Mediateway is scheduled for Date: $courtRoom->date at 11:00 AM. Join using this link. Thank you! Mediateway.";
                            $encodedMessage = urlencode($messageContent);
                            $description    = route('front.guest.livecourtroom', ['room_id' => $room_id]) . "?case_id=$case_id&message=$encodedMessage";

                            // Send Email
                            try {
                                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($subject, $email) {
                                    $message->to($email)->subject($subject);
                                });

                                Log::info("Arbitration Court Room Email sent successfully for FileCase ID: {$case_id}");
                            } catch (\Exception $e) {
                                Log::warning("Arbitration Court Room Email failed for FileCase ID: {$case_id}. Error: " . $e->getMessage());
                                $allEmailsSent = false;
                            }
                        }

                        // ✅ Update only if all emails succeeded
                        if ($allEmailsSent) {
                            $courtRoom->update([
                                'email_send_date'         => now(),
                                'send_mail_to_respondent' => 1,
                            ]);
                        } else {
                            // Optional: log or flag if partial or complete failure
                            Log::error("Arbitration Court Room Email Failed to send email to: $email");
                            $courtRoom->update(['send_mail_to_respondent' => 2]);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error creating courtroom entry: " . $th->getMessage());
        }
    }
}
