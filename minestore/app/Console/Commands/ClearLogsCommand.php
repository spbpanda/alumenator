<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class ClearLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing laravel.log file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            File::put($logFile, '');
            $this->info('Logs have been cleared!');
        } else {
            $this->error('Logs file not found!');
        }

        return 0;
    }
}
