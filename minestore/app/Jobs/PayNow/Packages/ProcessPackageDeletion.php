<?php

namespace App\Jobs\PayNow\Packages;

use App\Observers\ItemObserver;
use App\Models\Item;
use App\Models\PnProductReference;
use App\Integrations\PayNow\Management;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPackageDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Item $item;
    protected PnProductReference $pnProduct;
    protected Management $management;

    public function __construct(Item $item, PnProductReference $pnProduct, Management $management)
    {
        $this->item = $item;
        $this->pnProduct = $pnProduct;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            $observer = app(ItemObserver::class);
            $observer->deletePayNowProduct($this->item, $this->pnProduct, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessItemDeletion failed', [
                'item_id' => $this->item->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
