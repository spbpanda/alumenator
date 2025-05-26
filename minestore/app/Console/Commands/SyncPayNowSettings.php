<?php

namespace App\Console\Commands;

use App\Facades\PaynowManagement;
use App\Facades\PaynowStorefront;
use App\Models\PnSetting;
use App\Models\PnVatRate;
use App\Models\PnWebhook;
use App\Services\PayNowIntegrationService;
use Carbon\Carbon;
use Crypt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SyncPayNowSettings extends Command
{
    protected $signature = 'paynow:sync-settings';
    protected $description = 'Sync PayNow settings and tax rates';

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

        $this->info('Starting PayNow settings sync...');
        $now = Carbon::now();
        $this->info("Current time: {$now->toDateTimeString()}");

        $this->syncTaxRates();
        $this->syncStoreCurrency();
        $this->syncWebhooks();

        $this->info('PayNow settings synced successfully.');
    }

    private function syncTaxRates(): void
    {
        $pnVatRates = PnVatRate::first();

        if (!$pnVatRates || $pnVatRates->updated_at < Carbon::now()->subHour()) {
            try {
                $taxSyncProcess = PaynowStorefront::syncTaxRates();
                if (!$taxSyncProcess) {
                    $this->error('Failed to sync tax rates.');
                    Log::error('PayNow tax rates sync failed');
                    return;
                }
                $this->info('Tax rates synced successfully.');
            } catch (\Exception $e) {
                $this->error('Error syncing tax rates: ' . $e->getMessage());
                Log::error('PayNow tax rates sync error: ' . $e->getMessage());
            }
        } else {
            $this->info('Tax rates are up to date.');
        }
    }

    private function syncStoreCurrency(): void
    {
        $store = PaynowManagement::getStore();
        if ($store && isset($store['currency'])) {
            $currency = $store['currency'];
            $pnSetting = PnSetting::first();
            if ($pnSetting) {
                $pnSetting->enabled = PnSetting::STATUS_ENABLED;
                $pnSetting->store_id = $store['id'];
                $pnSetting->store_currency = $currency;
                $pnSetting->save();
            } else {
                PnSetting::create([
                    'enabled' => PnSetting::STATUS_ENABLED,
                    'store_id' => $store['id'],
                    'store_currency' => $currency
                ]);
            }

            $this->info("Store currency: {$currency}");
        } else {
            $this->error('Failed to retrieve store currency.');
            Log::error('PayNow store currency retrieval failed');
        }
    }

    private function syncWebhooks(): void
    {
        $this->info('Checking PayNow Webhooks...');
        $pnWebhook = PnWebhook::first();

        try {
            $webhooks = PaynowManagement::getWebhooks();
            if (!$webhooks || !is_array($webhooks)) {
                $this->error('Failed to retrieve webhooks.');
                Log::error('PayNow webhook retrieval failed');
                $this->createNewWebhook();
                return;
            }

            if ($pnWebhook) {
                $this->handleExistingWebhook($pnWebhook, $webhooks);
            } else {
                $this->handleNoWebhook($webhooks);
            }
        } catch (\Exception $e) {
            $this->error('Error managing webhooks: ' . $e->getMessage());
            Log::error('PayNow webhook sync error: ' . $e->getMessage());
        }
    }

    private function handleExistingWebhook(PnWebhook $pnWebhook, array $webhooks): void
    {
        $webhookExists = collect($webhooks)->contains('id', $pnWebhook->webhook_id);
        $correctUrl = config('app.url') . '/api/payments/handle/paynow';
        $correctUrl = str_replace('http://', 'https://', $correctUrl);

        if (!$webhookExists) {
            $this->info('Webhook not found in PayNow, creating new one...');
            $this->createNewWebhook();
            return;
        }

        if ($pnWebhook->url !== $correctUrl) {
            $this->info('Updating webhook URL...');
            try {
                $update = PaynowManagement::updateWebhook($pnWebhook->webhook_id, [
                    'url' => $correctUrl,
                ]);
                if ($update) {
                    $pnWebhook->url = $correctUrl;
                    $pnWebhook->secret = Crypt::encryptString($update['secret']);
                    $pnWebhook->save();
                    $this->info('Webhook URL updated successfully.');
                } else {
                    $this->error('Failed to update webhook URL.');
                    Log::error('PayNow webhook URL update failed');
                }
            } catch (\Exception $e) {
                $this->error('Error updating webhook URL: ' . $e->getMessage());
                Log::error('PayNow webhook URL update error: ' . $e->getMessage());
            }
        } else {
            $this->info("Webhook {$pnWebhook->webhook_id} is up to date.");
        }
    }

    private function handleNoWebhook(array $webhooks): void
    {
        $this->info('No local webhook found, cleaning up existing webhooks...');

        foreach ($webhooks as $webhook) {
            try {
                $this->info("Deleting existing webhook: {$webhook['id']}");
                $delete = PaynowManagement::deleteWebhook($webhook['id']);
                if ($delete) {
                    $this->info("Webhook deleted successfully: {$webhook['id']}");
                } else {
                    $this->error("Failed to delete webhook: {$webhook['id']}");
                    Log::error("PayNow webhook deletion failed: {$webhook['id']}");
                }
            } catch (\Exception $e) {
                $this->error("Error deleting webhook: {$e->getMessage()}");
                Log::error("PayNow webhook deletion error: {$e->getMessage()}");
            }
        }

        $this->createNewWebhook();
    }

    private function createNewWebhook(): void
    {
        $correctUrl = config('app.url') . '/api/payments/handle/paynow';
        $correctUrl = str_replace('http://', 'https://', $correctUrl);

        $data = [
            'subscribed_to' => 'OnOrderCompleted,OnSubscriptionActivated,OnSubscriptionRenewed,OnSubscriptionCanceled',
            'type' => 'JsonV1',
            'url' => $correctUrl,
        ];

        try {
            $newWebhook = PaynowManagement::createWebhook($data);
            if ($newWebhook && isset($newWebhook['id'])) {
                $pnWebhooks = PaynowManagement::getWebhooks();
                if ($pnWebhooks && is_array($pnWebhooks)) {
                    foreach ($pnWebhooks as $webhook) {
                        if ($webhook['id'] !== $newWebhook['id']) {
                            PaynowManagement::deleteWebhook($webhook['id']);
                        }
                    }
                }
                PnWebhook::truncate();
                $pnWebhook = new PnWebhook();
                $pnWebhook->webhook_id = $newWebhook['id'];
                $pnWebhook->url = $data['url'];
                $pnWebhook->secret = Crypt::encryptString($newWebhook['secret']);
                $pnWebhook->save();

                $this->info("New webhook created successfully: {$newWebhook['id']}");
            } else {
                $this->error('Failed to create new webhook.');
                Log::error('PayNow webhook creation failed');
            }
        } catch (\Exception $e) {
            $this->error('Error creating webhook: ' . $e->getMessage());
            Log::error('PayNow webhook creation error: ' . $e->getMessage());
        }
    }
}
