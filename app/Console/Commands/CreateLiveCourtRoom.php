<?php
namespace App\Console\Commands;

use App\Models\CourtRoom;
use App\Models\FileCase;
use Carbon\Carbon;
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
        // ##############################################
        // Create Live Court Room
        // ##############################################
        
        try {
            // Handle the three different hearing types
            $hearingTypes = [
                1 => 'first_hearing_date',
                2 => 'second_hearing_date',
                3 => 'final_hearing_date',
            ];
    
            foreach ($hearingTypes as $hearingType => $hearingDateColumn) {
                // Fetch cases with common arbitrator and case manager
                $fileCases = FileCase::with(['assignedCases' => function ($query) {
                        $query->select('case_id', 'arbitrator_id', 'case_manager_id');
                    }])
                    ->whereNotNull($hearingDateColumn)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->get()
                    ->groupBy($hearingDateColumn);
              
                foreach ($fileCases as $date => $cases) {
                    $courtroomData = CourtRoom::where('date', $date)->first();
                   
                    if (!$courtroomData) {
                        // Collect comma-separated IDs
                        $individual_ids = $cases->pluck('individual_id')->filter()->unique()->implode(',');
                        $organization_ids = $cases->pluck('organization_id')->filter()->unique()->implode(',');
                        $court_room_case_ids = $cases->pluck('id')->unique()->implode(',');
                       
                        // Common arbitrator and case manager (assume they are the same across grouped records)
                        $arbitrator_id = optional($cases->first()->assignedCases->first())->arbitrator_id;
                        $case_manager_id = optional($cases->first()->assignedCases->first())->case_manager_id;
    
                        // Generate Room ID based on Individual or Organization
                        $prefix = $individual_ids ? 'INDI' : 'ORG';
                        $lastRoom = CourtRoom::where('room_id', 'like', $prefix . '-%')->orderBy('id', 'desc')->first();
                        $nextNumber = $lastRoom ? ((int)str_replace($prefix . '-', '', $lastRoom->room_id) + 1) : 1;
                        $room_id = $prefix . '-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    
                        // Create the courtroom record
                        CourtRoom::create([
                            'room_id'               => $room_id,
                            'court_room_case_id'    => $court_room_case_ids,
                            'hearing_type'          => $hearingType,
                            'individual_id'         => $individual_ids ?? NULL,
                            'organization_id'       => $organization_ids ?? NULL,
                            'arbitrator_id'         => $arbitrator_id,
                            'case_manager_id'       => $case_manager_id,
                            'date'                  => $date,
                            'time'                  => '10:00:00', // 10:00 AM
                            'status'                => 0
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::error("Error creating courtroom entry: " . $th->getMessage());
        }

    }
}
