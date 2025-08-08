<?php
namespace App\Console\Commands;

use App\Models\CourtRoom;
use App\Models\CourtroomHearingLink;
use App\Models\FileCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        // ##################################
        // Create Live Arbitrator Court Room 
        // ##################################

        try {
            // Handle the three different hearing types
            $hearingTypes = [
                1 => 'first_hearing_date',
                2 => 'second_hearing_date',
                3 => 'final_hearing_date',
            ];

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
                       
                        // hearing link generate for all cases
                        foreach ($cases as $case) {
                            $hearingName = $hearingTypeLabels[$hearingType] ?? 'Hearing Link';
                            $case_id = $case->id;

                            $messageContent = "Your $hearingName at Mediateway is scheduled for Date: $courtRoom->date at 11:00 AM. Join using this link. Thank you! Mediateway.";
                            $link = route('front.guest.livecourtroom', ['room_id' => $room_id]) . "?case_id=$case_id";
                            $description = $link . "\n" . $messageContent;

                            $hearingLink = CourtroomHearingLink::create([
                                'file_case_id'     => $case_id,
                                'hearing_type'     => $hearingType,
                                'link'             => $description,
                                'date'             => $date,
                                'time'             => '11:00:00',
                                'email_status'     => 0,
                                'whatsapp_status'  => 0,
                                'sms_status'       => 0,
                            ]);

                            if ($hearingLink && $hearingLink->id) {
                                Log::info("CourtroomHearingLink created for FileCase ID: $case_id");
                            } else {
                                Log::warning("Failed to create CourtroomHearingLink for FileCase ID: $case_id");
                            }
                        }
                        // Optional: Log success
                        Log::info("Courtroom hearing links created for hearing type $hearingType on date $date for " . count($cases) . " cases.");
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error creating courtroom entry: " . $th->getMessage());
        }
    }
}
