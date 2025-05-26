<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Str;
use Storage;

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
        \App\Console\Commands\SyncPayNow::class,
        \App\Console\Commands\DisablePayNow::class,
        \App\Console\Commands\SyncPayNowSettings::class,
        \App\Console\Commands\ParsePayNowLogs::class,
        \App\Console\Commands\RotateLogsCommand::class,
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

        // Syncing PayNow entities
        $schedule->command('paynow:sync')->everyFifteenMinutes();
        $schedule->command('paynow:sync-settings')->hourly();
        $schedule->command('paynow:parse-logs')->everyMinute();
        $schedule->command('logs:rotate')->daily()->at('00:00');

        $schedule->call(function () {
            $files = Storage::disk('public')->files('img/items');
            foreach ($files as $file) {
                if (Str::startsWith(basename($file), 'temp_') && Storage::disk('public')->lastModified($file) < now()->subHours(24)->timestamp) {
                    Storage::disk('public')->delete($file);
                }
            }
        })->daily();

        $schedule->command('php artisan logs:clear')->daily()->at('00:00');
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
