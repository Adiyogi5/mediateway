<?php
namespace App\Console\Commands;

use App\Models\MediatorMeetingRoom;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StatusLiveMediatorMeetingRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:status-live-mediator-meeting-room';

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
        // Status Live Meeting Room
        // ##############################################

        $now = Carbon::now();
        $start = $now->copy()->subMinute();  // 1 minute before now
        $end = $now->copy()->addMinute();    // 1 minute after now

        $meetingroomData = MediatorMeetingRoom::where('date', Carbon::today())
            ->whereBetween('time', [$start->format('H:i:s'), $end->format('H:i:s')])
            ->where('status', 0)
            ->get();

        foreach ($meetingroomData as $value) {
            try {
                if (! empty($value->room_id)) {
                    $value->update([
                        'status' => 1,
                    ]);
                }
            } catch (\Throwable $th) {
                Log::error("Error updating meetingroom status for ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
