<?php

namespace App\Jobs\PayNow\Utility;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncPaynowSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 2;

    public function __construct()
    {
        $this->onQueue('paynow');
    }

    public function handle(): void
    {
        try {
            Artisan::call('paynow:sync-settings');
        } catch (\Throwable $exception) {
            Log::error('SyncPaynowSettingsJob failed', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            throw $exception;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('SyncPaynowSettingsJob failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
