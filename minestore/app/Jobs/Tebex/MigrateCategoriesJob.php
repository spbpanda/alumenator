<?php

namespace App\Jobs\Tebex;

use App\Integrations\Tebex\Migration;
use App\Models\Category;
use App\Models\PlatformMigrationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MigrateCategoriesJob implements ShouldQueue
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
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting MigrateCategoriesJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id
        ]);

        $tebex = app(Migration::class);
        $categories = $tebex->getCategories($this->identifier);

        if (!$categories['data']) {
            Log::warning('No categories found for migration', [
                'identifier' => $this->identifier,
                'migration_id' => $this->migration_id
            ]);
            return;
        }

        $categories = $categories['data'];
        Log::info('Retrieved categories', [
            'count' => count($categories),
            'identifier' => $this->identifier
        ]);

        foreach ($categories as $category) {
            if (empty($category['parent'])) {
                $this->createCategory($category, null);
            }
        }

        foreach ($categories as $category) {
            if (!empty($category['parent'])) {
                $parentId = $category['parent']['id'];
                $parentLog = PlatformMigrationLog::where('external_id', $parentId)
                    ->where('type', PlatformMigrationLog::TYPE_CATEGORY)
                    ->first();

                if (!$parentLog) {
                    Log::info('Parent category not found, creating parent', [
                        'parent_id' => $parentId,
                        'category_id' => $category['id']
                    ]);
                    $this->createCategory($category['parent'], null);
                }

                $this->createCategory($category, $parentId);
            }
        }

        Log::info('Completed MigrateCategoriesJob', [
            'identifier' => $this->identifier,
            'migration_id' => $this->migration_id
        ]);
    }

    private function createCategory($data, $parentId = null)
    {
        $categoryExists = PlatformMigrationLog::where('external_id', $data['id'])
            ->where('type', PlatformMigrationLog::TYPE_CATEGORY)
            ->first();

        if ($categoryExists) {
            Log::debug('Category already migrated, skipping', [
                'external_id' => $data['id'],
                'name' => $data['name']
            ]);
            return;
        }

        $createData = [
            'name' => $data['name'],
            'img' => null,
            'description' => $data['description'],
            'sorting' => $data['order'],
            'is_enable' => 1,
            'deleted' => 0,
        ];

        if ($parentId) {
            $internalParent = PlatformMigrationLog::where('external_id', $parentId)
                ->where('type', PlatformMigrationLog::TYPE_CATEGORY)
                ->first();

            if ($internalParent) {
                $createData['parent_id'] = $internalParent->internal_id;
                $parentCategory = Category::find($internalParent->internal_id);
                $createData['url'] = $parentCategory->url . '/' . $data['slug'];
            } else {
                Log::error('Parent category not found for child', [
                    'parent_id' => $parentId,
                    'child_id' => $data['id'],
                    'child_name' => $data['name']
                ]);
                $createData['url'] = $data['slug'];
            }
        } else {
            $createData['url'] = $data['slug'];
        }

        if ($data['display_type'] === 'list') {
            $createData['is_listing'] = 1;
        } elseif ($data['display_type'] === 'comparison') {
            $createData['is_comparison'] = 1;
        }

        try {
            $category = Category::create($createData);

            PlatformMigrationLog::create([
                'type' => PlatformMigrationLog::TYPE_CATEGORY,
                'internal_id' => $category->id,
                'external_id' => $data['id'],
                'migration_id' => $this->migration_id,
            ]);

            Log::info('Category created successfully', [
                'external_id' => $data['id'],
                'name' => $data['name'],
                'internal_id' => $category->id,
                'parent_id' => $parentId
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create category', [
                'external_id' => $data['id'],
                'name' => $data['name'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
