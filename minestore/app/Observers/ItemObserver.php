<?php

namespace App\Observers;

use App;
use App\Helpers\ChargeHelper;
use App\Helpers\SanitizeHelper;
use App\Jobs\PayNow\Packages\ProcessImageHandling;
use App\Jobs\PayNow\Packages\ProcessPackageCreation;
use App\Jobs\PayNow\Packages\ProcessPackageDeletion;
use App\Jobs\PayNow\Packages\ProcessPackageUpdate;
use App\Models\Item;
use App\Models\PnProductReference;
use App\Services\PayNowIntegrationService;
use App\Integrations\PayNow\Management;
use App\Helpers\ExpireHelper;
use App\Helpers\CommandHelper;
use Illuminate\Support\Facades\Log;

class ItemObserver
{
    /**
     * Prepare item data for PayNow API
     *
     * @param Item $item
     * @return array
     */
    public function preparePayNowData(Item $item): array
    {
        $data = [
            'name' => $item->name,
            'description' => $item->description,
            'slug' => SanitizeHelper::createSlug($item->name),
            'price' => (int) round($item->price * 100),
            'image_url' => url('img/items/' . $item->image),
            'remove_after_enabled' => $item->expireAfter > 0,
            'allow_subscription' => (bool)$item->is_subs,
            'tags' => [
                App\Models\PnCategoryReference::where('internal_category_id', $item->category_id)->first()->external_category_id ?? null,
            ],
            'gameservers' => CommandHelper::getPayNowItemServers($item),
            'commands' => CommandHelper::formatCommandsForPayNow($item),
            'metadata' => [
                'minestore_item_id' => (string)$item->id,
            ],
            'deliverable_actions' => [
                'grant_giftcard' => $item->giftcard_price > 0,
            ],
            'is_hidden' => $item->active == 0,
            'is_gifting_disabled' => true,
            'tax_code' => $item->is_subs ? 'digital_goods_subscription_gaming' : 'digital_goods_permanent_gaming',
        ];

        if ($item->is_subs) {
            $data['allow_one_time_purchase'] = $item->is_subs_only === 0;
            $data['subscription_interval_value'] = ChargeHelper::GetChargeDays($item->chargePeriodUnit, $item->chargePeriodValue);
            $data['subscription_interval_scale'] = 'day';
        }

        if ($item->expireAfter > 0) {
            $data['remove_after_time_value'] = $item->expireAfter;
            $data['remove_after_time_scale'] = ExpireHelper::getStringUnitValue($item->expireUnit);
        }

        return $data;
    }

    /**
     * Sync item with PayNow
     *
     * @param Item $item
     * @param bool $imageChanged
     * @return void
     */
    protected function syncWithPayNow(Item $item, bool $imageChanged = false): void
    {
        try {
            $paynowService = App::make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled() || !$paynowService->validateRequest()) {
                return;
            }

            $management = App::make(Management::class);
            $data = $this->preparePayNowData($item);
            $pnProduct = PnProductReference::where('internal_package_id', $item->id)->first();

            $processImage = $imageChanged || !empty($item->image);

            if (!$pnProduct) {
                $job = ProcessPackageCreation::dispatch($item, $data, $management);
                if ($processImage) {
                    $job->chain([
                        new ProcessImageHandling($item->id, $management)
                    ]);
                }
            } else {
                $job = ProcessPackageUpdate::dispatch($item, $pnProduct, $data, $management);
                if ($processImage) {
                    $job->chain([
                        new ProcessImageHandling($item->id, $management)
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] ItemObserver: sync exception', [
                'item_id' => $item->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Create product in PayNow
     *
     * @param Item $item
     * @param array $data
     * @param Management $management
     * @return void
     */
    public function createPayNowProduct(Item $item, array $data, Management $management): void
    {
        $externalProduct = $management->createProduct($data);

        if ($externalProduct) {
            $pnProduct = PnProductReference::create([
                'internal_package_id' => $item->id,
                'external_package_id' => $externalProduct['id'],
                'external_package_price' => $externalProduct['price'],
            ]);
        } else {
            Log::error('[PayNow] ItemObserver: product creation failed', [
                'item_id' => $item->id,
            ]);
        }
    }

    /**
     * Update product in PayNow
     *
     * @param Item $item
     * @param PnProductReference $pnProduct
     * @param array $data
     * @param Management $management
     * @return void
     */
    public function updatePayNowProduct(Item $item, PnProductReference $pnProduct, array $data, Management $management): void
    {
        $result = $management->updateProduct($pnProduct->external_package_id, $data);

        if ($result) {
            Log::info('[PayNow] ItemObserver: product updated successfully', [
                'item_id' => $item->id,
                'external_package_id' => $pnProduct->external_package_id,
            ]);

            $pnProduct->update([
                'external_package_price' => $result['price'],
            ]);
        } else {
            Log::error('[PayNow] ItemObserver: product update failed', [
                'item_id' => $item->id,
                'external_package_id' => $pnProduct->external_package_id,
            ]);
        }
    }

    /**
     * Delete product in PayNow
     *
     * @param Item $item
     * @param PnProductReference $pnProduct
     * @param Management $management
     * @return void
     */
    public function deletePayNowProduct(Item $item, PnProductReference $pnProduct, Management $management): void
    {
        $result = $management->deleteProduct($pnProduct);

        if ($result) {
            Log::info('[PayNow] ItemObserver: product deleted from PayNow', [
                'item_id' => $item->id,
                'external_package_id' => $pnProduct->external_package_id,
            ]);

            $pnProduct->delete();
        } else {
            Log::error('[PayNow] ItemObserver: product deletion failed', [
                'item_id' => $item->id,
                'external_package_id' => $pnProduct->external_package_id,
            ]);
        }
    }

    /**
     * Handle image management for PayNow Product
     *
     * @param Item $item
     * @param PnProductReference $pnProduct
     * @param Management $management
     *
     * @return void
     */
    public function handleImage(Item $item, PnProductReference $pnProduct, Management $management): void
    {
        $imageExists = $management->checkProductImage($pnProduct);

        if ($imageExists) {
            Log::info('[PayNow] ItemObserver: product image already exists. Removing old image.', [
                'item_id' => $item->id,
                'external_package_id' => $pnProduct->external_package_id,
            ]);
            $management->deleteProductImage($pnProduct->external_package_id);
        }

        $result = $management->uploadImageToProduct($item, $pnProduct->external_package_id);
        Log::info('[PayNow] ItemObserver: product image upload result', [
            'item_id' => $item->id,
            'result' => $result,
            'image' => $item->image,
        ]);
    }

    // Flag to track recently created items
    private static $recentlyCreatedItems = [];

    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        Log::info('Item created', [
            'item_id' => $item->id,
            'name' => $item->name,
            'has_image' => !empty($item->image),
            'image_value' => $item->image,
            'image_type' => gettype($item->image),
            'image_updated_at' => $item->image_updated_at,
        ]);

        self::$recentlyCreatedItems[$item->id] = true;

        // Pass image change status based on image_updated_at
        $imageChanged = !is_null($item->image_updated_at);
        $this->syncWithPayNow($item, $imageChanged);
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        if (isset(self::$recentlyCreatedItems[$item->id])) {
            Log::info('[PayNow] ItemObserver: skipping update for newly created item', ['id' => $item->id]);
            unset(self::$recentlyCreatedItems[$item->id]);
            return;
        }

        $significantChanges = array_diff(array_keys($item->getDirty()), ['sorting', 'updated_at']);

        if (empty($significantChanges)) {
            Log::info('[PayNow] ItemObserver: no significant changes, skipping sync', ['id' => $item->id]);
            return;
        }

        Log::info('[PayNow] ItemObserver: item updated with significant changes', [
            'item_id' => $item->id,
            'changes' => $significantChanges,
        ]);

        $imageChanged = $item->wasChanged('image') || $item->wasChanged('image_updated_at');
        $this->syncWithPayNow($item, $imageChanged);

        if ($item->deleted && $item->wasChanged('deleted')) {
            $pnProduct = PnProductReference::where('internal_package_id', $item->id)->first();
            if ($pnProduct) {
                $management = App::make(Management::class);
                $this->deletePayNowProduct($item, $pnProduct, $management);
            }
        }
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        try {
            $paynowService = App::make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled()) {
                return;
            }

            $pnProduct = PnProductReference::where('internal_package_id', $item->id)->first();

            if ($pnProduct) {
                $management = App::make(Management::class);
                ProcessPackageDeletion::dispatch($item, $pnProduct, $management);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] ItemObserver: delete exception', [
                'item_id' => $item->id,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Item "restored" event.
     */
    public function restored(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "force deleted" event.
     */
    public function forceDeleted(Item $item): void
    {
        //
    }
}
