<?php

namespace App\Jobs\PayNow\Categories;

use App\Observers\CategoryObserver;
use App\Models\Category;
use App\Integrations\PayNow\Management;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCategoryCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Category $category;
    protected array $data;
    protected Management $management;

    public function __construct(Category $category, array $data, Management $management)
    {
        $this->category = $category;
        $this->data = $data;
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
            $observer->createPayNowTag($this->category, $this->data, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessCategoryCreation failed', [
                'category_id' => $this->category->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
