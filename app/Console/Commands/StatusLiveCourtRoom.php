<?php
namespace App\Console\Commands;

use App\Models\CourtRoom;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StatusLiveCourtRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:status-live-court-room';

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
        // Status Live Court Room
        // ##############################################
        $courtroomData = CourtRoom::where('date', Carbon::today())
            ->where('time', '=', Carbon::now()->format('H:i:s'))
            ->where('status', 0)
            ->get();

        foreach ($courtroomData as $value) {
            try {
                if (! empty($value->room_id)) {
                    $value->update([
                        'status' => 1,
                    ]);
                }
            } catch (\Throwable $th) {
                Log::error("Error updating courtroom status for ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
