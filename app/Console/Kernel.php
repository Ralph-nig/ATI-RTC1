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
        // Generate maintenance warnings daily at 8:00 AM
        $schedule->command('maintenance:generate-warnings')
            ->dailyAt('08:00')
            ->appendOutputTo(storage_path('logs/maintenance-warnings.log'));
        
        // Alternative: Run every hour (for testing or more frequent checks)
        // $schedule->command('maintenance:generate-warnings')
        //     ->hourly()
        //     ->appendOutputTo(storage_path('logs/maintenance-warnings.log'));
        
        // Clean up old notifications (optional - every week)
        // $schedule->command('notifications:clean-old')
        //     ->weekly()
        //     ->sundays()
        //     ->at('02:00');
        
        // Example: Database backup (if you have backup package)
        // $schedule->command('backup:run')
        //     ->daily()
        //     ->at('01:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}