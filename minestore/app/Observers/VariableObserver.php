<?php

namespace App\Observers;

use App;
use App\Helpers\CommandHelper;
use App\Helpers\SanitizeHelper;
use App\Integrations\PayNow\Management;
use App\Jobs\PayNow\Variables\ProcessVariableCreation;
use App\Jobs\PayNow\Variables\ProcessVariableDeletion;
use App\Jobs\PayNow\Variables\ProcessVariableUpdate;
use App\Models\PnVariableReference;
use App\Models\Variable;
use App\Services\PayNowIntegrationService;
use Illuminate\Support\Facades\Log;

class VariableObserver
{
    /**
     * Prepare Variable data for item creation through PayNow API
     *
     * @param Variable $variable
     * @return array
     */
    public function preparePayNowData(Variable $variable): array
    {
        $category = SanitizeHelper::ensureVariableTagCategory();

        $data = [];
        foreach ($variable->variables as $option) {
            if ($option['price'] <= 0) {
                continue;
            }

            $data[] = [
                'name' => $option['name'] . ' Variable (' . $variable->name . ')',
                'description' => $variable->description,
                'slug' => SanitizeHelper::createSlug($option['name'] . ' Variable ' . $variable->name),
                'price' => (int)round($option['price'] * 100),
                'gameservers' => CommandHelper::getFirstPayNowServer(),
                'commands' => [
                    [
                        'stage' => 'on_purchase',
                        'content' => "ms variable {$option['name']} attach {username} {$option['value']}",
                        'online_only' => false,
                        'override_execute_on_gameserver_ids' => [],
                    ]
                ],
                'tags' => [
                    $category,
                ],
                'metadata' => [
                    'minestore_variable_value' => $option['value'],
                ],
                'allow_subscription' => false,
                'remove_after_enabled' => false,
                'is_hidden' => (bool)$variable->deleted,
                'tax_code' => 'digital_goods_permanent_gaming',
                'is_gifting_disabled' => true,
            ];
        }

        return $data;
    }

    /**
     * Sync Variable (by creating product) with PayNow
     *
     * @param Variable $variable
     * @return void
     */
    protected function syncVariableWithPayNow(Variable $variable): void
    {
        try {
            $paynowService = App::make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled() || !$paynowService->validateRequest()) {
                return;
            }

            $management = App::make(Management::class);
            $pnProduct = PnVariableReference::where('variable_id', $variable->id)
                ->get();

            if ($pnProduct->isEmpty()) {
                ProcessVariableCreation::dispatch($variable, $this->preparePayNowData($variable), $management);
            } else {
                ProcessVariableUpdate::dispatch($variable, $this->preparePayNowData($variable), $management);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] VariableObserver:syncVariableWithPayNow', [
                'variable_id' => $variable->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Create variable (as the product) in PayNow
     *
     * @param Variable $variable
     * @param array $data
     * @param Management $management
     *
     * @return void
     */
    public function createVariable(Variable $variable, array $data, Management $management): void
    {
        try {
            foreach ($data as $product) {
                $externalProduct = $management->createProduct($product);

                if ($externalProduct) {
                    Log::info('[PayNow] VariableObserver: variable (product) created', [
                        'variable_id' => $variable->id,
                        'external_product_id' => $externalProduct['id'],
                    ]);

                    // Save the external product ID to the database
                    PnVariableReference::updateOrCreate(
                        [
                            'variable_id' => $variable->id,
                            'value' => $product['metadata']['minestore_variable_value'],
                        ],
                        [
                            'external_product_id' => $externalProduct['id'],
                            'external_product_price' => $externalProduct['price'],
                        ]
                    );
                } else {
                    Log::info('[PayNow] VariableObserver:createVariableInPayNow', [
                        'variable_id' => $variable->id,
                        'external_product_id' => null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] VariableObserver: variable (product) creation failed', [
                'variable_id' => $variable->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Update variable (as the product) in PayNow
     *
     * @param Variable $variable
     * @param array $data
     * @param Management $management
     *
     * @return void
     */
    public function updateVariable(Variable $variable, array $data, Management $management): void
    {
        try {
            foreach ($data as $variableProduct) {
                $pnVariable = PnVariableReference::where('variable_id', $variable->id)
                    ->where('value', $variableProduct['metadata']['minestore_variable_value'])
                    ->first();

                if ($pnVariable) {
                    $result = $management->updateProduct($pnVariable->external_product_id, $variableProduct);

                    if ($result) {
                        Log::info('[PayNow] VariableObserver: variable (product) updated', [
                            'variable_id' => $variable->id,
                            'external_product_id' => $pnVariable->external_product_id,
                        ]);

                        $pnVariable->update([
                            'external_product_price' => $variableProduct['price'],
                        ]);
                    } else {
                        Log::error('[PayNow] VariableObserver: variable (product) update failed', [
                            'variable_id' => $variable->id,
                            'external_product_id' => $pnVariable->external_product_id,
                        ]);
                    }
                } else {
                    Log::error("[PayNow] VariableObserver: variable (product): {$variable->id} not found in PayNow. Creating it.");
                    $externalProduct = $management->createProduct($variableProduct);

                    if ($externalProduct) {
                        Log::info('[PayNow] VariableObserver: variable (product) created', [
                            'variable_id' => $variable->id,
                            'external_product_id' => $externalProduct['id'],
                        ]);

                        // Save the external product ID to the database
                        PnVariableReference::updateOrCreate(
                            [
                                'variable_id' => $variable->id,
                                'value' => $variableProduct['metadata']['minestore_variable_value'],
                            ],
                            [
                                'external_product_id' => $externalProduct['id'],
                                'external_product_price' => $variableProduct['price'],
                            ]
                        );
                    } else {
                        Log::error('[PayNow] VariableObserver: variable (product) creation failed', [
                            'variable_id' => $variable->id,
                            'external_product_id' => null,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] VariableObserver: variable (product) update failed', [
                'variable_id' => $variable->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Delete variable (as the product) in PayNow
     *
     * @param Variable $variable
     * @param Management $management
     *
     * @return void
     */
    public function deleteVariable(Variable $variable, Management $management): void
    {
        if ($variable->type != Variable::DROPDOWN_TYPE) {
            return;
        }

        try {
            $pnVariable = PnVariableReference::where('variable_id', $variable->id)
                ->get();

            if ($pnVariable->isEmpty()) {
                Log::info('[PayNow] VariableObserver: variable (product) not found in PayNow', [
                    'variable_id' => $variable->id,
                ]);
                return;
            }

            foreach ($pnVariable as $product) {
                $result = $management->deleteVariableProduct($product);

                if ($result) {
                    Log::info('[PayNow] VariableObserver: variable (product) deleted', [
                        'variable_id' => $variable->id,
                        'external_product_id' => $product->external_product_id,
                    ]);

                    $product->delete();
                } else {
                    Log::error('[PayNow] VariableObserver: variable (product) deletion failed', [
                        'variable_id' => $variable->id,
                        'external_product_id' => $product->external_product_id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] VariableObserver: variable (product) deletion failed', [
                'variable_id' => $variable->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the Variable "created" event.
     */
    public function created(Variable $variable): void
    {
        if ($variable->type == Variable::DROPDOWN_TYPE) {
            Log::info('Variable created', [
                'variable_id' => $variable->id,
                'name' => $variable->name,
            ]);

            $this->syncVariableWithPayNow($variable);
        }
    }

    /**
     * Handle the Variable "updated" event.
     */
    public function updated(Variable $variable): void
    {
        if ($variable->type != Variable::DROPDOWN_TYPE) {
            ProcessVariableDeletion::dispatch($variable);
            return;
        }

        if ($variable->deleted) {
            ProcessVariableDeletion::dispatch($variable);
            return;
        }

        Log::info('Variable updated', [
            'variable_id' => $variable->id,
            'name' => $variable->name,
        ]);

        $this->syncVariableWithPayNow($variable);
    }

    /**
     * Handle the Variable "deleted" event.
     */
    public function deleted(Variable $variable): void
    {
        if ($variable->type != Variable::DROPDOWN_TYPE) {
            return;
        }

        Log::info('Variable deleted', [
            'variable_id' => $variable->id,
            'name' => $variable->name,
        ]);

        ProcessVariableDeletion::dispatch($variable);
    }

    /**
     * Handle the Variable "restored" event.
     */
    public function restored(Variable $variable): void
    {
        //
    }

    /**
     * Handle the Variable "force deleted" event.
     */
    public function forceDeleted(Variable $variable): void
    {
        //
    }
}
