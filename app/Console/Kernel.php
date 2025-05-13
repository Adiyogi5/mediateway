<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('db:backup')->twiceDaily(6, 18, 3);
        
        $schedule->command('bulk:create-live-court-room')->everyOneMinutes();
        $schedule->command('bulk:create-claim-petition')->everyOneMinutes();

        $schedule->command('bulk:send-2b-notice')->everyOneMinutes();
        $schedule->command('bulk:send-3a-notice')->everyOneMinutes();
        $schedule->command('bulk:send-3b-notice')->everyOneMinutes();
        $schedule->command('bulk:send-3c-notice')->everyOneMinutes();
        $schedule->command('bulk:send-4a-notice')->everyOneMinutes();
        $schedule->command('bulk:send-5a-notice')->everyOneMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
