<?php

namespace App\Jobs\PayNow\Packages;

use App\Integrations\PayNow\Management;
use App\Models\Item;
use App\Models\PnProductReference;
use App\Observers\ItemObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImageHandling implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $itemId;
    protected Management $management;

    public function __construct($itemId, Management $management)
    {
        $this->itemId = $itemId;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            Log::info('[PayNow] ProcessImageHandling: starting', [
                'item_id' => $this->itemId,
            ]);

            $item = Item::findOrFail($this->itemId);
            $pnProduct = PnProductReference::where('internal_package_id', $this->itemId)->first();

            if (!$pnProduct) {
                return;
            }

            if (empty($item->image)) {
                return;
            }

            $observer = app(ItemObserver::class);
            $observer->handleImage($item, $pnProduct, $this->management);

            Log::info('[PayNow] ProcessImageHandling: completed', [
                'item_id' => $this->itemId,
            ]);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessImageHandling: failed', [
                'item_id' => $this->itemId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
