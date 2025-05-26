<?php

namespace App\Jobs\PayNow\Categories;

use App\Observers\CategoryObserver;
use App\Models\Category;
use App\Models\PnCategoryReference;
use App\Integrations\PayNow\Management;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCategoryDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Category $category;
    protected PnCategoryReference $pnCategory;
    protected Management $management;

    public function __construct(Category $category, PnCategoryReference $pnCategory, Management $management)
    {
        $this->category = $category;
        $this->pnCategory = $pnCategory;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            $observer = app(CategoryObserver::class);
            $observer->deletePayNowTag($this->category, $this->pnCategory, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessCategoryDeletion failed', [
                'category_id' => $this->category->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
