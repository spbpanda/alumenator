<?php

namespace App\Jobs\PayNow\Utility;

use App\Models\PnSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnablePaynowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pnSetting = PnSetting::first();
            if ($pnSetting) {
                $pnSetting->update([
                    'enabled' => PnSetting::STATUS_ENABLED
                ]);
            } else {
                Log::warning('No PnSetting record found in EnablePaynowJob.');
            }
        } catch (\Exception $e) {
            Log::error('EnablePaynowJob failed: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
