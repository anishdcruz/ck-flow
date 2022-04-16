<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendExportEmails;
use App\RecurringExport;
use Schema;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\LangGenerator::class,
        Commands\Installer::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if(!Schema::hasTable('recurring_exports')) {
            return ;
        }
        $exports = RecurringExport::all();
        $filePath = storage_path('logs/scheduler.log');

        foreach($exports as $export) {
            if($export->frequency == 'daily') {
                // dd($export->send_at);
                $schedule->job(new SendExportEmails($export))
                    ->dailyAt($export->send_at)
                    ->appendOutputTo($filePath);
            } else if($export->frequency == 'weekly') {
                $schedule->job(new SendExportEmails($export))
                    ->weeklyOn($export->send_on, $export->send_at)
                    ->appendOutputTo($filePath);
            } else if($export->frequency == 'monthly') {
                $schedule->job(new SendExportEmails($export))
                    ->monthlyOn($export->send_on, $export->send_at)
                    ->appendOutputTo($filePath);
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
