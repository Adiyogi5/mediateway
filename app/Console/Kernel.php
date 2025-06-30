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
        
        // $schedule->command('bulk:create-live-court-room')->everyTenMinutes();
        // $schedule->command('bulk:status-live-court-room')->everyTenMinutes();

        // $schedule->command('bulk:create-live-conciliator-meeting-room')->everyTenMinutes();
        // $schedule->command('bulk:status-live-conciliator-meeting-room')->everyTenMinutes();

        // $schedule->command('bulk:create-live-mediator-meeting-room')->everyTenMinutes();
        // $schedule->command('bulk:status-live-mediator-meeting-room')->everyTenMinutes();

        // $schedule->command('bulk:create-claim-petition')->everyTenMinutes();

        // $schedule->command('bulk:send-1-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-1b-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-2b-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-3a-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-3b-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-3c-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-4a-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-5a-notice')->everyTenMinutes();

        $schedule->command('bulk:preconciliation-notice-email-send')->everyTenMinutes();
        $schedule->command('bulk:preconciliation-notice-whatsapp-send')->everyTenMinutes();
        $schedule->command('bulk:preconciliation-notice-sms-send')->everyTenMinutes();
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
