<?php

namespace App\Observers;

use App;
use App\Helpers\SanitizeHelper;
use App\Integrations\PayNow\Management;
use App\Jobs\PayNow\Categories\ProcessCategoryCreation;
use App\Jobs\PayNow\Categories\ProcessCategoryDeletion;
use App\Jobs\PayNow\Categories\ProcessCategoryUpdate;
use App\Models\Category;
use App\Models\PnCategoryReference;
use App\Services\PayNowIntegrationService;
use Illuminate\Support\Facades\Log;

class CategoryObserver
{
    /**
     * Prepare Category data for PayNow API
     *
     * @param Category $category
     * @return array
     */
    public function preparePayNowData(Category $category): array
    {
        return [
            'slug' => SanitizeHelper::makeSlug($category->url),
            'name' => $category->name,
            'description' => $category->description,
            'enabled' => (bool)$category->is_enable,
        ];
    }

    /**
     * Sync category (tag) with PayNow
     *
     * @param Category $category
     * @return void
     */
    protected function syncWithPayNow(Category $category): void
    {
        try {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled() || !$paynowService->validateRequest()) {
                return;
            }

            $management = App::make(Management::class);

            $data = $this->preparePayNowData($category);
            $pnCategory = PnCategoryReference::where('internal_category_id', $category->id)->first();

            if (!$pnCategory) {
                ProcessCategoryCreation::dispatch($category, $data, $management);
            } else {
                ProcessCategoryUpdate::dispatch($category, $data, $management, $pnCategory);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to sync category with PayNow: ' . $e->getMessage());
        }
    }

    /**
     * Create tag in PayNow
     *
     * @param Category $category
     * @param array $data
     * @param Management $management
     * @return void
     */
    public function createPayNowTag(Category $category, array $data, Management $management): void
    {
        $externalId = $management->createTag($data);

        if ($externalId) {
            Log::info('[PayNow] CategoryObserver: tag created successfully', [
                'category_id' => $category->id,
                'external_category_id' => $externalId,
            ]);

            // Save the external category ID to the database
            PnCategoryReference::create([
                'internal_category_id' => $category->id,
                'external_category_id' => $externalId,
            ]);
        } else {
            Log::error('[PayNow] Failed to create tag in PayNow', [
                'category_id' => $category->id,
                'data' => $data,
            ]);
        }
    }

    /**
     * Update tag in PayNow
     *
     * @param Category $category
     * @param array $data
     * @param Management $management
     * @param PnCategoryReference $pnCategory
     * @return void
     */
    public function updatePayNowTag(Category $category, array $data, Management $management, PnCategoryReference $pnCategory): void
    {
        $externalId = $pnCategory->external_category_id;

        if ($management->updateTag($externalId, $data)) {
            Log::info('[PayNow] CategoryObserver: tag updated successfully', [
                'category_id' => $category->id,
                'external_category_id' => $externalId,
            ]);
        } else {
            Log::error('[PayNow] Failed to update tag in PayNow', [
                'category_id' => $category->id,
                'data' => $data,
            ]);
        }
    }

    /**
     * Delete tag in PayNow
     *
     * @param Category $category
     * @param PnCategoryReference $pnCategory
     * @param Management $management
     *
     * @return void
     */
    public function deletePayNowTag(Category $category, PnCategoryReference $pnCategory, Management $management): void
    {
        $result = $management->deleteTag($pnCategory);

        if ($result) {
            Log::info('[PayNow] CategoryObserver: tag deleted successfully', [
                'category_id' => $category->id,
                'external_category_id' => $pnCategory->external_category_id,
            ]);

            $pnCategory->delete();
        } else {
            Log::error('[PayNow] Failed to delete tag in PayNow', [
                'category_id' => $category->id,
                'external_category_id' => $pnCategory->external_category_id,
            ]);
        }
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        Log::info('[PayNow] Category created', [
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        $this->syncWithPayNow($category);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $significantChanges = array_diff(array_keys($category->getDirty()), ['sorting', 'updated_at']);

        if (empty($significantChanges)) {
            Log::info('[PayNow] CategoryObserver: no significant changes, skipping sync', ['id' => $category->id]);
            return;
        }

        Log::info('[PayNow] Category updated', [
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        $this->syncWithPayNow($category);
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        try {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled()) {
                return;
            }

            $pnCategory = PnCategoryReference::where('internal_category_id', $category->id)->first();

            if ($pnCategory) {
                $management = App::make(Management::class);
                ProcessCategoryDeletion::dispatch($category, $pnCategory, $management);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] CategoryObserver delete exception', [
                'category_id' => $category->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
