<?php

namespace App\Console\Commands;

use App\Facades\PaynowManagement;
use App\Helpers\SanitizeHelper;
use App\Models\PnCategoryReference;
use App\Models\PnProductReference;
use App\Models\PnServerReference;
use App\Models\PnSyncLog;
use App\Models\PnVariableReference;
use App\Models\Server;
use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Variable;
use App\Jobs\PayNow\Servers\ProcessServerCreation;
use App\Jobs\PayNow\Servers\ProcessServerUpdate;
use App\Jobs\PayNow\Servers\ProcessServerDeletion;
use App\Jobs\PayNow\Categories\ProcessCategoryCreation;
use App\Jobs\PayNow\Categories\ProcessCategoryUpdate;
use App\Jobs\PayNow\Categories\ProcessCategoryDeletion;
use App\Jobs\PayNow\Packages\ProcessPackageCreation;
use App\Jobs\PayNow\Packages\ProcessPackageUpdate;
use App\Jobs\PayNow\Packages\ProcessPackageDeletion;
use App\Jobs\PayNow\Packages\ProcessImageHandling;
use App\Jobs\PayNow\Variables\ProcessVariableCreation;
use App\Jobs\PayNow\Variables\ProcessVariableUpdate;
use App\Jobs\PayNow\Variables\ProcessVariableDeletion;
use App\Observers\CategoryObserver;
use App\Observers\ItemObserver;
use App\Observers\ServerObserver;
use App\Observers\VariableObserver;
use App\Services\PayNowIntegrationService;
use App\Integrations\PayNow\Management;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class SyncPayNow extends Command
{
    protected $signature = 'paynow:sync';
    protected $description = 'Sync categories, servers, items, and variables with PayNow in prioritized order';

    public function handle()
    {
        $paynowService = App::make(PayNowIntegrationService::class);
        if (!$paynowService->isPaymentMethodEnabled()) {
            $this->info('PayNow is disabled. Skipping synchronization.');
            return;
        }

        if (!$paynowService->validateRequest()) {
            $this->error('PayNow request validation failed. Please, check your configuration.');
            return;
        }

        $this->info('Starting PayNow synchronization...');

        $lastSync = PnSyncLog::where('component', 'item')->first();
        $isFirstSync = false;
        if (!$lastSync) {
            $this->info('No previous sync log found. Syncing all items.');
            $isFirstSync = true;
        }

        try {
            $settings = Setting::first();
            if (!$settings) {
                $this->error('Settings not found. Please, ensure the application is properly configured.');
                return;
            }

            $settings->update([
                'details' => 0,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to initialize PayNow integration: ' . $e->getMessage());
        }

        try {
            $storeData = [
                'website_url' => config('app.url'),
            ];

            PaynowManagement::updateStoreSettings($storeData);
        } catch (\Throwable $e) {
            Log::error('Failed to update PayNow store settings: ' . $e->getMessage());
        }

        // Create a variable tag category if it doesn't exist
        SanitizeHelper::ensureVariableTagCategory();

        // Sync components in priority order: Categories, Servers, Items, Variables
        $this->syncComponent('category', Category::class, [
            'create' => ProcessCategoryCreation::class,
            'update' => ProcessCategoryUpdate::class,
            'delete' => ProcessCategoryDeletion::class,
        ], true);

        $this->syncComponent('server', Server::class, [
            'create' => ProcessServerCreation::class,
            'update' => ProcessServerUpdate::class,
            'delete' => ProcessServerDeletion::class,
        ], true);

        $this->syncComponent('item', Item::class, [
            'create' => ProcessPackageCreation::class,
            'update' => ProcessPackageUpdate::class,
            'delete' => ProcessPackageDeletion::class,
            'image' => ProcessImageHandling::class,
        ]);

        $this->syncComponent('variable', Variable::class, [
            'create' => ProcessVariableCreation::class,
            'update' => ProcessVariableUpdate::class,
            'delete' => ProcessVariableDeletion::class,
        ]);

        $this->syncImages($isFirstSync, App::make(Management::class));

        $this->info('PayNow synchronization completed.');
    }

    protected function syncComponent(string $component, string $modelClass, array $jobs, bool $synchronous = false)
    {
        $lastSync = PnSyncLog::where('component', $component)->first();
        $lastSyncedAt = $lastSync ? $lastSync->last_synced_at : null;

        if ($lastSyncedAt === null) {
            Log::info("[PayNow] First sync for {$component}. Syncing all records.");
        } else {
            Log::info("[PayNow] Last sync for {$component} was at {$lastSyncedAt}. Syncing new records.");
        }

        // Query records updated since last sync, using cursor for memory efficiency
        $query = $modelClass::query()->where('deleted', 0);
        if ($lastSyncedAt) {
            $query->where('updated_at', '>', $lastSyncedAt);
        }

        $management = App::make(Management::class);
        $recordCount = 0;

        foreach ($query->cursor() as $record) {
            $this->dispatchSyncJobs($component, $record, $management, $jobs, $synchronous);
            $recordCount++;
        }

        if ($recordCount === 0) {
            Log::info("[PayNow] No changes detected for {$component}. Checking deleted records.");
        } else {
            Log::info("[PayNow] Queued {$recordCount} {$component} records for sync.");
        }

        // Syncing deleted records
        $this->syncDeletedComponent($component, $modelClass, $jobs['delete'], $synchronous);

        if ($recordCount >= 0) {
            PnSyncLog::updateOrCreate(
                ['component' => $component],
                ['last_synced_at' => now()]
            );
        }
    }

    protected function syncDeletedComponent(string $component, string $modelClass, string $deleteJob, bool $synchronous = false): int
    {
        $lastSync = PnSyncLog::where('component', $component)->first();
        $lastSyncedAt = $lastSync ? $lastSync->last_synced_at : null;

        if (!in_array(SoftDeletes::class, class_uses_recursive($modelClass))) {
            Log::info("[PayNow] Soft deletes not enabled for {$component}. Skipping deleted records sync.");
            return 0;
        }

        $query = $modelClass::onlyTrashed();
        if ($lastSyncedAt) {
            $query->where('deleted_at', '>', $lastSyncedAt);
        }

        $management = App::make(Management::class);
        $deletedCount = 0;

        foreach ($query->cursor() as $record) {
            $this->dispatchDeleteJob($component, $record, $management, $deleteJob, $synchronous);
            $deletedCount++;
        }

        if ($deletedCount === 0) {
            Log::info("[PayNow] No deleted {$component} records to sync.");
        } else {
            Log::info("[PayNow] Queued {$deletedCount} deleted {$component} records for sync.");
        }

        return $deletedCount;
    }

    protected function dispatchSyncJobs(string $component, $record, Management $management, array $jobs, bool $synchronous = false)
    {
        try {
            $reference = $this->getReference($component, $record->id);
            $data = $this->preparePayNowData($component, $record);

            // For Items, checking if Category reference exists
            if ($component === 'item') {
                $categoryId = $record->category_id;
                if ($categoryId && !PnCategoryReference::where('internal_category_id', $categoryId)->exists()) {
                    Log::warning("[PayNow] Skipping item creation: Category reference not found", [
                        'item_id' => $record->id,
                        'category_id' => $categoryId,
                    ]);
                    return;
                }
            }

            $existsInPayNow = $reference ? $this->checkExistence($component, $reference, $management) : null;

            if (!$reference || !$existsInPayNow) {
                if ($component === 'server' && !method_exists(ServerObserver::class, 'createPayNowServer')) {
                    Log::warning("[PayNow] Skipping server creation: createServer method not found in ServerObserver", [
                        'server_id' => $record->id,
                    ]);
                    return;
                }

                if ($synchronous) {
                    $job = new $jobs['create']($record, $data, $management);
                    $job->handle();
                } else {
                    $jobs['create']::dispatch($record, $data, $management);
                }
            } else {
                if ($component === 'variable') {
                    // Variable update: Variable, array, Management
                    if ($synchronous) {
                        $job = new $jobs['update']($record, $data, $management);
                        $job->handle();
                    } else {
                        $jobs['update']::dispatch($record, $data, $management);
                    }
                } elseif ($component === 'item') {
                    // Item update: Item, PnProductReference, array, Management
                    if ($synchronous) {
                        $job = new $jobs['update']($record, $reference, $data, $management);
                        $job->handle();
                    } else {
                        $jobs['update']::dispatch($record, $reference, $data, $management);
                    }
                } else {
                    // Server and Category update: Model, array, Management, Reference
                    if ($synchronous) {
                        $job = new $jobs['update']($record, $data, $management, $reference);
                        $job->handle();
                    } else {
                        $jobs['update']::dispatch($record, $data, $management, $reference);
                    }
                }
            }

            // Handle image for item
            if ($component === 'item' && isset($jobs['image']) && $reference && ($record->wasChanged('image') || $record->wasChanged('image_updated_at'))) {
                if ($synchronous) {
                    $job = new $jobs['image']($record, $reference, $management);
                    $job->handle();
                } else {
                    $jobs['image']::dispatch($record, $reference, $management);
                }
            }
        } catch (\Exception $e) {
            Log::error("[PayNow] Failed to dispatch sync jobs for {$component}", [
                'id' => $record->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function dispatchDeleteJob(string $component, $record, Management $management, string $deleteJob, bool $synchronous = false): void
    {
        try {
            $reference = $this->getReference($component, $record->id);
            if ($reference) {
                if ($component === 'variable') {
                    // Variable deletion: only Variable
                    if ($synchronous) {
                        $job = new $deleteJob($record);
                        $job->handle();
                    } else {
                        $deleteJob::dispatch($record);
                    }
                } else {
                    // Other deletions: Model, Reference, Management
                    if ($synchronous) {
                        $job = new $deleteJob($record, $reference, $management);
                        $job->handle();
                    } else {
                        $deleteJob::dispatch($record, $reference, $management);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("[PayNow] Failed to dispatch delete job for {$component}", [
                'id' => $record->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function getReference(string $component, int $id)
    {
        return match ($component) {
            'server' => PnServerReference::where('internal_server_id', $id)->first(),
            'category' => PnCategoryReference::where('internal_category_id', $id)->first(),
            'item' => PnProductReference::where('internal_package_id', $id)->first(),
            'variable' => PnVariableReference::where('variable_id', $id)->first(),
            default => null,
        };
    }

    protected function checkExistence(string $component, $reference, Management $management): ?bool
    {
        return match ($component) {
            'item' => $management->getProduct($reference->external_package_id ?? $reference->external_product_id) !== null,
            'category', 'server', 'variable' => true,
            default => false,
        };
    }

    protected function preparePayNowData(string $component, $record): array
    {
        return match ($component) {
            'item' => (new ItemObserver)->preparePayNowData($record),
            'category' => (new CategoryObserver)->preparePayNowData($record),
            'server' => (new ServerObserver)->preparePayNowData($record),
            'variable' => (new VariableObserver)->preparePayNowData($record),
            default => [],
        };
    }

    protected function syncImages($isFirstSync, $management): void
    {
        if ($isFirstSync) {
            $this->info('Waiting for 5 minutes before syncing images...');
            sleep(300); // Wait for 5 minutes
            $this->info('Syncing images...');
            $pnProducts = PnProductReference::all();

            $syncCount = 0;
            $skipCount = 0;
            foreach ($pnProducts as $pnProduct) {
                $item = $pnProduct->item;

                if ($item && $item->image) {
                    $this->info("Syncing image for PayNow product ID: {$pnProduct->id}");
                    ProcessImageHandling::dispatch($item->id, $management);
                    $syncCount++;
                } else {
                    $this->info("Skipping PayNow product ID: {$pnProduct->id} - No updated image found");
                    $skipCount++;
                }
            }

            $this->info("Image synchronization completed. Synced: $syncCount, Skipped: $skipCount");
        } else {
            $this->info('No need to sync images. Skipping...');
        }
    }
}
