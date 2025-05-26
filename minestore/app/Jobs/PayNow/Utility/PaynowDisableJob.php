<?php

namespace App\Jobs\PayNow\Utility;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaynowDisableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 2;
    public const CACHE_KEY = 'paynow_settings';

    /**
     * Execute the job.
     */
    public function handle()
    {
        Artisan::call('paynow:disable');

        try {
            $cacheKey = self::CACHE_KEY;
            $apiKey = self::CACHE_KEY . '_api_key';
            Cache::forget($cacheKey);
            Cache::forget($apiKey);
            return true;
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to purge cache.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('PayNow disable command failed: ' . $exception->getMessage());
    }
}
