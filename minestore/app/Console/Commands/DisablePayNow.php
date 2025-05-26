<?php

namespace App\Console\Commands;

use App\Integrations\PayNow\Management;
use App\Jobs\PayNow\Packages\ProcessPackageDeletion;
use App\Models\Category;
use App\Models\Item;
use App\Models\PnAlert;
use App\Models\PnCategoryReference;
use App\Models\PnProductReference;
use App\Models\PnServerReference;
use App\Models\PnSetting;
use App\Models\PnSyncLog;
use App\Models\PnVariableReference;
use App\Models\PnWebhook;
use App\Models\Server;
use App\Models\Variable;
use App\Observers\CategoryObserver;
use App\Observers\ItemObserver;
use App\Observers\ServerObserver;
use App\Observers\VariableObserver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DisablePayNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paynow:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will disable the PayNow integration and remove all references to it.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Disabling PayNow Integration started.');
        $management = App::make(Management::class);

        try {
            // Get the list of all products
            $productReferences = PnProductReference::all();
            $itemObserver = app(ItemObserver::class);
            foreach ($productReferences as $productReference) {
                try {
                    $this->info('Deleting product reference: ' . $productReference->id);
                    $item = Item::where('id', $productReference->internal_package_id)->first();
                    if ($item) {
                        $itemObserver->deletePayNowProduct($item, $productReference, $management);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Product reference deleted: ' . $productReference->id);
                    }
                } catch (\Exception $e) {
                    $this->error('Failed to delete product reference: ' . $productReference->id . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing product references: ' . $e->getMessage());
        }

        try {
            // Delete all variable references
            $variableReferences = PnVariableReference::all();
            $variableObserver = app(VariableObserver::class);
            foreach ($variableReferences as $variableReference) {
                try {
                    $this->info('Deleting variable reference: ' . $variableReference->id);
                    $variable = Variable::where('id', $variableReference->variable_id)->first();
                    if ($variable) {
                        $variableObserver->deleteVariable($variable, $management);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Variable reference deleted: ' . $variableReference->id);
                    }
                } catch (\Exception $e) {
                    $this->error('Failed to delete variable reference: ' . $variableReference->id . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing variable references: ' . $e->getMessage());
        }

        try {
            sleep(2);
            // Getting all remaining PayNow products and deleting them
            $remainingPayNowProducts = $management->getProducts();
            if ($remainingPayNowProducts && count($remainingPayNowProducts) > 0) {
                foreach ($remainingPayNowProducts as $product) {
                    try {
                        $this->info('Deleting remaining PayNow product: ' . $product['id']);
                        $management->deleteRemainProduct($product['id']);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Remaining PayNow product deleted: ' . $product['id']);
                    } catch (\Exception $e) {
                        $this->error('Failed to delete remaining PayNow product: ' . $product['id'] . ' - ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing remaining PayNow products: ' . $e->getMessage());
        }

        try {
            // Delete all tags (categories)
            $categoryReferences = PnCategoryReference::all();
            $categoryObserver = app(CategoryObserver::class);
            foreach ($categoryReferences as $categoryReference) {
                try {
                    $this->info('Deleting category reference: ' . $categoryReference->id);
                    $category = Category::where('id', $categoryReference->internal_category_id)->first();
                    if ($category) {
                        $categoryObserver->deletePayNowTag($category, $categoryReference, $management);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Category reference deleted: ' . $categoryReference->id);
                    }
                } catch (\Exception $e) {
                    $this->error('Failed to delete category reference: ' . $categoryReference->id . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing category references: ' . $e->getMessage());
        }

        try {
            // Getting all remaining PayNow categories and deleting them
            $remainingPayNowCategories = $management->getTags();
            if ($remainingPayNowCategories) {
                foreach ($remainingPayNowCategories as $category) {
                    try {
                        $this->info('Deleting remaining PayNow category: ' . $category['id']);
                        $management->deleteTag($category['id']);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Remaining PayNow category deleted: ' . $category['id']);
                    } catch (\Exception $e) {
                        $this->error('Failed to delete remaining PayNow category: ' . $category['id'] . ' - ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing remaining PayNow categories: ' . $e->getMessage());
        }

        try {
            // Delete all servers
            $serverReferences = PnServerReference::all();
            $serverObserver = app(ServerObserver::class);
            foreach ($serverReferences as $serverReference) {
                try {
                    $this->info('Deleting server reference: ' . $serverReference->id);
                    $server = Server::where('id', $serverReference->internal_server_id)->first();
                    if ($server) {
                        $serverObserver->deletePayNowServer($server, $serverReference, $management);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Server reference deleted: ' . $serverReference->id);
                    }
                } catch (\Exception $e) {
                    $this->error('Failed to delete server reference: ' . $serverReference->id . ' - ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing server references: ' . $e->getMessage());
        }

        try {
            // Getting all remaining PayNow servers and deleting them
            $remainingPayNowServers = $management->getServers();
            if ($remainingPayNowServers) {
                foreach ($remainingPayNowServers as $server) {
                    try {
                        $this->info('Deleting remaining PayNow server: ' . $server['id']);
                        $management->deleteServer($server['id']);
                        usleep(100000); // Sleep for 0.4 seconds to avoid hitting the API rate limit
                        $this->info('Remaining PayNow server deleted: ' . $server['id']);
                    } catch (\Exception $e) {
                        $this->error('Failed to delete remaining PayNow server: ' . $server['id'] . ' - ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing remaining PayNow servers: ' . $e->getMessage());
        }

        try {
            // Delete all references to PayNow in the database
            PnProductReference::truncate();
            PnVariableReference::truncate();
            PnCategoryReference::truncate();
            PnServerReference::truncate();
            PnSyncLog::truncate();

            $pnSettings = PnSetting::first();
            if ($pnSettings) {
                $pnSettings->update([
                    'enabled' => 0,
                    'variable_tag_id' => null,
                ]);
            }

            $this->info('All PayNow references deleted from the database.');
        } catch (\Exception $e) {
            $this->error('Error truncating PayNow tables: ' . $e->getMessage());
        }

        try {
            $cacheKey = 'paynow_settings';
            $apiKey = $cacheKey . '_api_key';
            Cache::forget($cacheKey);
            Cache::forget($apiKey);
            return true;
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to purge cache.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
