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
        // $schedule->command('db:backup')->twiceDaily(6, 18, 3);
        
        // $schedule->command('bulk:create-live-court-room')->dailyAt('00:00');
        // $schedule->command('bulk:status-live-court-room')->everyMinute();

        // $schedule->command('bulk:create-live-email-conciliator-meeting-room')->dailyAt('00:00')();
        // $schedule->command('bulk:create-live-whatsapp-conciliator-meeting-room')->dailyAt('00:00')();
        // $schedule->command('bulk:status-live-conciliator-meeting-room')->everyMinute();

        // $schedule->command('bulk:create-live-email-mediator-meeting-room')->dailyAt('00:00')();
        // $schedule->command('bulk:create-live-whatsapp-mediator-meeting-room')->dailyAt('00:00')();
        // $schedule->command('bulk:status-live-mediator-meeting-room')->everyMinute();

        // $schedule->command('bulk:send-1-notice')->everyTenMinutes();
        // $schedule->command('bulk:send-1b-notice')->everyTenMinutes();

        $schedule->command('bulk:send-email-2b-notice')->everyMinute();
        $schedule->command('bulk:send-whatsapp-2b-notice')->everyMinute();
        $schedule->command('bulk:send-sms-2b-notice')->everyMinute();

        $schedule->command('bulk:send-email-3a-notice')->everyMinute();
        // $schedule->command('bulk:send-whatsapp-3a-notice')->everyMinute();
        $schedule->command('bulk:send-sms-3a-notice')->everyMinute();

        $schedule->command('bulk:send-email-3b-notice')->everyMinute();
        // $schedule->command('bulk:send-whatsapp-3b-notice')->everyMinute();
        $schedule->command('bulk:send-sms-3b-notice')->everyMinute();
        
        $schedule->command('bulk:send-email-3c-notice')->everyMinute();
        // $schedule->command('bulk:send-whatsapp-3c-notice')->everyMinute();
        $schedule->command('bulk:send-sms-3c-notice')->everyMinute();

        $schedule->command('bulk:create-claim-petition')->everyMinute();

        $schedule->command('bulk:send-email-4a-notice')->everyMinute();
        $schedule->command('bulk:send-whatsapp-4a-notice')->everyMinute();
        $schedule->command('bulk:send-sms-4a-notice')->everyMinute();

        // $schedule->command('bulk:send-email-5a-notice')->everyMinute();
        // $schedule->command('bulk:send-whatsapp-5a-notice')->everyMinute();
        // $schedule->command('bulk:send-sms-5a-notice')->everyMinute();


        $schedule->command('bulk:preconciliation-notice-pdf-save')->everyMinute();
        $schedule->command('bulk:preconciliation-notice-email-send')->everyMinute();
        $schedule->command('bulk:preconciliation-notice-whatsapp-send')->everyMinute();
        $schedule->command('bulk:preconciliation-notice-sms-send')->everyMinute();

        $schedule->command('bulk:conciliation-notice-email-send')->everyMinute();
        $schedule->command('bulk:conciliation-notice-whatsapp-send')->everyMinute();
        $schedule->command('bulk:conciliation-notice-sms-send')->everyMinute();


        $schedule->command('bulk:premediation-notice-pdf-save')->everyMinute();
        $schedule->command('bulk:premediation-notice-email-send')->everyMinute();
        $schedule->command('bulk:premediation-notice-whatsapp-send')->everyMinute();
        $schedule->command('bulk:premediation-notice-sms-send')->everyMinute();

        $schedule->command('bulk:mediation-notice-email-send')->everyMinute();
        $schedule->command('bulk:mediation-notice-whatsapp-send')->everyMinute();
        $schedule->command('bulk:mediation-notice-sms-send')->everyMinute();
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
