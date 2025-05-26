<?php

namespace App\Jobs\Tebex;

use App\Integrations\Tebex\Migration;
use App\Models\Item;
use App\Models\PlatformMigrationLog;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Str;

class MigratePackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $identifier;
    protected int $migration_id;

    /**
     * Create a new job instance.
     */
    public function __construct($identifier, $migration_id)
    {
        $this->identifier = $identifier;
        $this->migration_id = $migration_id;
    }

    /**
     * Download an image from URL and store it temporarily.
     */
    private function downloadAndStoreImage(string $imageUrl): ?string
    {
        try {
            $response = Http::timeout(10)->get($imageUrl);

            if ($response->successful()) {
                $tempFilename = 'temp_' . time() . '_' . Str::random(7) . '.png';
                Storage::disk('public')->put('img/items/' . $tempFilename, $response->body());

                Log::info('Image downloaded successfully', [
                    'url' => $imageUrl,
                    'temp_filename' => $tempFilename
                ]);

                return $tempFilename;
            } else {
                Log::warning('Failed to download image, HTTP status: ' . $response->status(), [
                    'url' => $imageUrl
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while downloading image', [
                'url' => $imageUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting MigratePackagesJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id
        ]);

        $tebex = app(Migration::class);
        $packages = $tebex->getPackages($this->identifier);

        if (empty($packages['data'])) {
            Log::warning('No packages found for migration', [
                'identifier' => $this->identifier,
                'migration_id' => $this->migration_id
            ]);
            return;
        }

        $packages = $packages['data'];
        Log::info('Retrieved packages', [
            'count' => count($packages),
            'identifier' => $this->identifier
        ]);

        foreach ($packages as $package) {
            $this->createPackage($package);
        }

        Log::info('Completed MigratePackagesJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id
        ]);
    }

    /**
     * Create a package in the system from Tebex package data
     */
    private function createPackage($data): void
    {
        $packageExists = PlatformMigrationLog::where('external_id', $data['id'])
            ->where('type', PlatformMigrationLog::TYPE_PACKAGE)
            ->first();

        if ($packageExists) {
            Log::debug('Package already migrated, skipping', [
                'external_id' => $data['id'],
                'name' => $data['name']
            ]);
            return;
        }

        // Find the migrated category
        $categoryId = null;
        if (!empty($data['category']) && !empty($data['category']['id'])) {
            $categoryLog = PlatformMigrationLog::where('external_id', $data['category']['id'])
                ->where('type', PlatformMigrationLog::TYPE_CATEGORY)
                ->first();

            if ($categoryLog) {
                $categoryId = $categoryLog->internal_id;
            } else {
                Log::warning('Category not found for package', [
                    'category_id' => $data['category']['id'],
                    'package_id' => $data['id'],
                    'package_name' => $data['name']
                ]);
            }
        }

        $type = Item::MINECRAFT_PACKAGE;
        $is_subs = $data['type'] === 'subscription' ? 1 : 0;

        $chargePeriodValue = 0;
        $chargePeriodUnit = 0;

        $price = $data['base_price'];

        $tempImageName = null;
        if (!empty($data['image'])) {
            try {
                $tempImageName = $this->downloadAndStoreImage($data['image']);
            } catch (\Exception $e) {
                Log::error('Failed to download image for package', [
                    'package_id' => $data['id'],
                    'image_url' => $data['image'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $createData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'image' => $tempImageName,
            'price' => $price,
            'discount' => 0,
            'sorting' => $data['order'] ?? 0,
            'category_id' => $categoryId,
            'type' => $type,
            'is_subs' => $is_subs,
            'chargePeriodValue' => $chargePeriodValue,
            'chargePeriodUnit' => $chargePeriodUnit,
            'active' => 1,
            'deleted' => 0,
            'giftcard_price' => 0,
            'expireAfter' => 0,
            'expireUnit' => 0,
            'req_type' => Item::NO_REQ_TYPE,
            'featured' => 0,
            'is_subs_only' => $is_subs,
            'is_virtual_currency_only' => 0,
            'is_any_price' => 0,
            'is_server_choice' => 0,
            'quantityUserLimit' => null,
            'quantityUserPeriodValue' => 0,
            'quantityUserPeriodUnit' => 0,
            'quantityGlobalLimit' => null,
            'quantityGlobalPeriodValue' => 0,
            'quantityGlobalPeriodUnit' => 0,
        ];

        if (isset($data['disable_quantity']) && $data['disable_quantity']) {
            $createData['quantityUserLimit'] = 1;
        }

        try {
            $item = Item::create($createData);

            if ($tempImageName) {
                $finalFilename = $item->id . '.png';
                Storage::disk('public')->move('img/items/' . $tempImageName, 'img/items/' . $finalFilename);
                $item->update([
                    'image' => $finalFilename,
                    'image_updated_at' => Carbon::now()
                ]);
            }

            PlatformMigrationLog::create([
                'type' => PlatformMigrationLog::TYPE_PACKAGE,
                'internal_id' => $item->id,
                'external_id' => $data['id'],
                'migration_id' => $this->migration_id,
            ]);

            Log::info('Package created successfully', [
                'external_id' => $data['id'],
                'name' => $data['name'],
                'internal_id' => $item->id,
                'category_id' => $categoryId
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create package', [
                'external_id' => $data['id'],
                'name' => $data['name'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
