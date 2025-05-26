<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RotateLogsCommand extends Command
{
    protected $signature = 'logs:rotate';

    protected $description = 'Rotate Laravel log files, archiving the current log file and creating a new one';

    public function handle()
    {
        $logsDir = storage_path('logs');
        $currentLogFile = $logsDir . '/laravel.log';
        $lockFile = $logsDir . '/rotate.lock';

        if (File::exists($lockFile)) {
            $this->error('Another instance of log rotation is running!');
            return 1;
        }

        File::put($lockFile, '');

        try {
            if (!File::exists($currentLogFile)) {
                $this->error('Current log file not found!');
                return 1;
            }

            $filePerms = fileperms($currentLogFile);

            $today = Carbon::now()->format('Y-m-d');
            $archivedLogFile = $logsDir . '/laravel_' . $today . '.log';

            if (File::exists($archivedLogFile)) {
                File::append($archivedLogFile, File::get($currentLogFile));
            } else {
                File::copy($currentLogFile, $archivedLogFile);
            }

            File::put($currentLogFile, '');

            chmod($currentLogFile, $filePerms);

            $this->info('Current log file has been archived to: ' . $archivedLogFile);
            $this->info('New empty log file has been created with the same permissions');

            $this->deleteOldLogs($logsDir, 7);

            return 0;
        } finally {
            File::delete($lockFile);
        }
    }

    protected function deleteOldLogs($directory, $days)
    {
        $now = Carbon::now();
        $files = File::files($directory);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (preg_match('/laravel_[\d]{4}-[\d]{2}-[\d]{2}(_[\d]{6})?\.log$/', $file->getFilename())) {
                preg_match('/laravel_([\d]{4}-[\d]{2}-[\d]{2})/', $file->getFilename(), $matches);

                if (isset($matches[1])) {
                    $fileDate = Carbon::createFromFormat('Y-m-d', $matches[1]);

                    if ($now->diffInDays($fileDate) > $days) {
                        File::delete($file->getPathname());
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} log files older than {$days} days");
        }
    }
}
