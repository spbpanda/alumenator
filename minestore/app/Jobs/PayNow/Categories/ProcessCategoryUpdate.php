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

class ProcessCategoryUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Category $category;
    protected array $data;
    protected Management $management;
    protected PnCategoryReference $pnCategory;

    public function __construct(Category $category, array $data, Management $management, PnCategoryReference $pnCategory)
    {
        $this->category = $category;
        $this->data = $data;
        $this->management = $management;
        $this->pnCategory = $pnCategory;
        $this->onQueue('paynow');
    }

    public function handle(): void
    {
        try {
            $observer = app(CategoryObserver::class);
            $observer->updatePayNowTag($this->category, $this->data, $this->management, $this->pnCategory);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessCategoryUpdate failed', [
                'category_id' => $this->category->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
