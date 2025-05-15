<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CurrencyUpdate::class,
        \App\Console\Commands\CronWorker::class,
        \App\Console\Commands\ReportGenerate::class,
        \App\Console\Commands\CleanupPendingPayments::class,
        \App\Console\Commands\SearchIndexGenerate::class,
        \App\Console\Commands\DeactivateActiveCartsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('pending_payments:cleanup')->everyThreeHours();
        $schedule->command('report:generate')->monthly();

        // Generate the random time for currency update
        $hour = rand(0, 2);
        $minute = rand(0, 59);
        $randomTime = Carbon::createFromTime($hour, $minute)->format('H:i');

        $schedule->command('currency:update')->dailyAt($randomTime);
        $schedule->command('minestore:deactivate-carts')->hourly();
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
