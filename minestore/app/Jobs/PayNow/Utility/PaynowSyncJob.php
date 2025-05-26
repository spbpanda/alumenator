<?php

namespace App\Jobs\PayNow\Utility;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaynowSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 2;

    public function handle(): void
    {
        Log::info('Starting PaynowSyncJob', [
            'job_id' => $this->job->getJobId(),
            'queue' => $this->job->getQueue(),
        ]);

        try {
            $logFile = storage_path('logs/paynow-sync.log');

            $process = new Process(['php', 'artisan', 'paynow:sync']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout($this->timeout);

            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException('Paynow:sync failed: ' . $process->getErrorOutput());
            }

            $output = $process->getOutput() . $process->getErrorOutput();
            Storage::append('logs/paynow-sync.log', $output);

            Log::info('PaynowSyncJob completed paynow:sync', [
                'log_file' => $logFile,
                'process_id' => $process->getPid(),
                'output' => $output,
            ]);

        } catch (\Throwable $exception) {
            Log::error('PaynowSyncJob failed to execute paynow:sync', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }

        Log::info('PaynowSyncJob handle method completed');
    }

    public function failed(\Throwable $exception)
    {
        Log::error('PaynowSyncJob failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
