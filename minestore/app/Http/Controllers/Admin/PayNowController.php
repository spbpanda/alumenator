<?php

namespace App\Http\Controllers\Admin;

use App\Facades\PaynowManagement;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePayNowRequest;
use App\Http\Requests\StorePayNowRequest;
use App\Jobs\PayNow\Utility\EnablePaynowJob;
use App\Jobs\PayNow\Utility\ParsePaynowLogsJob;
use App\Jobs\PayNow\Utility\PaynowDisableJob;
use App\Jobs\PayNow\Utility\PaynowSyncJob;
use App\Jobs\PayNow\Utility\SyncPaynowSettingsJob;
use App\Models\PnHistory;
use App\Models\PnSetting;
use App\Models\PnSyncLog;
use App\Models\PnVatRate;
use App\Models\Setting;
use App\Services\PayNowIntegrationService;
use Carbon\Carbon;
use Crypt;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayNowController extends Controller
{
    protected PayNowIntegrationService $payNowService;

    public function __construct(PayNowIntegrationService $payNowService)
    {
        $this->payNowService = $payNowService;
    }

    public function onBoardingWelcome()
    {
        return view('admin.paynow.onboarding');
    }

    public function onBoardingStart()
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect()->route('paynow.onboarding.welcome')->with('error', 'You do not have permission to access this page.');
        }

        $isConfigured = $this->isPayNowConfigured();
        if ($isConfigured) {
            return redirect()->route('paynow.index');
        }

        return view('admin.paynow.onboarding-start');
    }

    public function onBoarding(CreatePayNowRequest $request)
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validated();

        try {
            $pnSettings = DB::transaction(function () use ($validatedData) {
                $pnSettings = PnSetting::first();
                $apiKey = Crypt::encryptString($validatedData['api_key']);

                if ($pnSettings) {
                    $pnSettings->update([
                        'enabled' => PnSetting::STATUS_ENABLED,
                        'store_id' => $validatedData['store_id'],
                        'api_key' => $apiKey,
                        'tax_mode' => PnSetting::TAX_MODE_EXCLUSIVE,
                    ]);
                } else {
                    $pnSettings = PnSetting::create([
                        'enabled' => PnSetting::STATUS_ENABLED,
                        'store_id' => $validatedData['store_id'],
                        'api_key' => $apiKey,
                        'tax_mode' => PnSetting::TAX_MODE_EXCLUSIVE,
                    ]);
                }

                $pnSettings->refresh();
                if (empty($pnSettings->api_key) || empty($pnSettings->store_id)) {
                    throw new \Exception('Failed to save PnSetting: empty api_key or store_id.');
                }

                return $pnSettings;
            });

            $purgeCache = PaynowManagement::purgeCache();
            if (!$purgeCache) {
                Log::error('Failed to purge cache after PnSetting save.');
            }

            Cache::forget(PayNowIntegrationService::CACHE_KEY);
            Cache::forget(PayNowIntegrationService::CACHE_KEY . '_api_key');
            $settings = PnSetting::first();
            Cache::put(PayNowIntegrationService::CACHE_KEY, $settings, PayNowIntegrationService::CACHE_TTL);
            if ($settings && !empty($settings->api_key)) {
                try {
                    $decryptedApiKey = Crypt::decryptString($settings->api_key);
                    Cache::put(PayNowIntegrationService::CACHE_KEY . '_api_key', $decryptedApiKey, PayNowIntegrationService::CACHE_TTL);
                } catch (\Exception $e) {
                    Log::error('Failed to cache decrypted API key: ' . $e->getMessage());
                }
            }

            $store = PaynowManagement::getStore();
            if (!$store) {
                Log::error('Failed to retrieve PayNow store data during onboarding.');
                $pnSettings->delete();
                PaynowManagement::purgeCache();

                return redirect()->route('paynow.onboarding.welcome')
                    ->with('error', 'Failed to retrieve PayNow Account Data. Please check your Store ID and API Key.');
            }

            $storeId = $validatedData['store_id'];

            PnHistory::create([
                'type' => PnHistory::TYPE_WARNING,
                'event' => PnHistory::EVENT_APPLICATION_SUBMITTED,
                'timeline' => true,
                'message' => 'You have applied your store for PayNow Integration. Waiting for approval.',
                'details' => ['store_id' => $storeId],
                'created_at' => now(),
            ]);

            PnHistory::create([
                'type' => PnHistory::TYPE_WARNING,
                'event' => PnHistory::EVENT_MODERATION_IN_PROGRESS,
                'timeline' => true,
                'message' => 'Your application is currently under moderation. Please wait for the approval.',
                'details' => ['store_id' => $storeId],
                'created_at' => now(),
            ]);

            try {
                $storeData = [
                    'store_tax_inclusive_pricing' => false,
                ];
                PaynowManagement::updateStoreSettings($storeData);
            } catch (\Throwable $e) {
                Log::error('Failed to update PayNow store settings: ' . $e->getMessage());
                return redirect()->route('paynow.onboarding.welcome')
                    ->with('error', 'Failed to update PayNow store settings.');
            }

            if ($pnSettings->enabled && !empty($pnSettings->api_key) && !empty($pnSettings->store_id)) {
                Bus::chain([
                    new PaynowDisableJob(),
                    new EnablePaynowJob(),
                    new SyncPaynowSettingsJob(),
                    new PaynowSyncJob(),
                    new ParsePaynowLogsJob(),
                ])
                ->catch(function ($e) {
                    Log::error('PayNow onboarding chain failed: ' . $e->getMessage());
                })->dispatch();
            } else {
                Log::error('[PayNow] Cannot start job chain: incomplete PnSetting.');
                return redirect()->route('paynow.onboarding.welcome')
                    ->with('error', 'PayNow settings are incomplete.');
            }

            return redirect()->route('paynow.index')
                ->with('success', 'PayNow store created successfully. Refresh the page after 5-10 minutes to see the integration status.');
        } catch (\Throwable $e) {
            Log::error('PayNow onboarding failed: ' . $e->getMessage());
            return redirect()->route('paynow.onboarding.welcome')
                ->with('error', 'Failed to complete PayNow onboarding: ' . $e->getMessage());
        }
    }

    public function index()
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $purgeCache = PaynowManagement::purgeCache();
        if (!$purgeCache) {
            Log::error('Failed to purge cache during PayNow onboarding.');
        }

        $isExists = $this->wasRegistered();
        if (!$isExists) {
            return redirect()->route('paynow.onboarding.welcome');
        }

        $payNowStore = PaynowManagement::getStore();
        $webstorePrimaryCurrency = Setting::firstOrFail()->currency;
        $syncData = $this->getSyncing();
        $taxSync = $this->taxSyncing();

        $pnSettings = PnSetting::first();
        $config = [
            'enabled' => $pnSettings && (bool)$pnSettings->enabled,
            'api_key' => $pnSettings && $pnSettings->api_key ? Crypt::decryptString($pnSettings->api_key) : '',
            'storefront_id' => $pnSettings ? $pnSettings->store_id : '',
            'tax_mode' => $pnSettings ? $pnSettings->tax_mode : 0,
        ];

        // Initialize diagnostics array
        $diagnostics = $this->initializeDiagnostics();

        // Update integration diagnostics
        $diagnostics['integration'] = $this->getIntegrationDiagnostics($payNowStore);

        // Update currency diagnostics
        $diagnostics['currency'] = $this->getCurrencyDiagnostics(
            $webstorePrimaryCurrency,
            $payNowStore
        );

        // Update sync diagnostics
        $diagnostics['sync'] = $this->getSyncDiagnostics($syncData);

        // Update tax sync diagnostics
        $diagnostics['taxSync'] = $this->getTaxSyncDiagnostics($taxSync);

        // Check for issues
        $issues = $this->checkForIssues($diagnostics, $payNowStore);

        // Update integration status if issues exist
        if ($issues['hasIssues'] && $diagnostics['integration']['status'] !== 'not_configured') {
            $diagnostics['integration'] = [
                'status' => 'needs_attention',
                'badge' => 'warning',
                'icon' => 'bx-error-circle',
                'message' => 'Needs Attention',
                'description' => null
            ];
        }

        // Prepare view data
        return view('admin.paynow.index', [
            'errorMessage' => $payNowStore ? null : 'PayNow store not found. Please check your PayNow settings.',
            'timelineEvents' => $this->getTimelineEvents(),
            'webstorePrimaryCurrency' => $webstorePrimaryCurrency,
            'payNowStore' => $payNowStore,
            'syncData' => $syncData,
            'syncVatRates' => $taxSync,
            'diagnostics' => $diagnostics,
            'config' => $config,
        ]);
    }

    public function store(StorePayNowRequest $request)
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $purgeCache = PaynowManagement::purgeCache();
        if (!$purgeCache) {
            Log::error('Failed to purge cache during PayNow onboarding.');
        }

        $validatedData = $request->validated();
        $pnSettings = PnSetting::first();

        $originalEnabled = $pnSettings ? $pnSettings->enabled : PnSetting::STATUS_DISABLED;
        $originalStoreId = $pnSettings ? $pnSettings->store_id : null;
        $originalApiKey = $pnSettings ? $pnSettings->api_key : null;

        try {
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $uploadLogo = PaynowManagement::uploadStoreLogo(1, $logo);

                if (!$uploadLogo) {
                    return redirect()->route('paynow.index')->with('error', 'Failed to upload logo.');
                }
            }

            if ($request->hasFile('logo_square')) {
                $logoSquare = $request->file('logo_square');
                $uploadLogoSquare = PaynowManagement::uploadStoreLogo(2, $logoSquare);

                if (!$uploadLogoSquare) {
                    return redirect()->route('paynow.index')->with('error', 'Failed to upload square logo.');
                }
            }

            $apiKey = Crypt::encryptString($validatedData['api_key']);

            $newEnabled = $validatedData['enabled'] ? PnSetting::STATUS_ENABLED : PnSetting::STATUS_DISABLED;
            $newTaxMode = $validatedData['tax_mode'] ? PnSetting::TAX_MODE_INCLUSIVE : PnSetting::TAX_MODE_EXCLUSIVE;

            if ($pnSettings) {
                $pnSettings->update([
                    'store_id' => $validatedData['store_id'],
                    'api_key' => $apiKey,
                    'tax_mode' => $newTaxMode,
                ]);
            } else {
                PnSetting::create([
                    'enabled' => $newEnabled,
                    'store_id' => $validatedData['store_id'],
                    'api_key' => $apiKey,
                    'tax_mode' => $newTaxMode,
                ]);
            }

            if ($newEnabled == PnSetting::STATUS_ENABLED) {
                $pnSettings->update([
                    'enabled' => PnSetting::STATUS_ENABLED,
                ]);
            }

            Cache::forget(PayNowIntegrationService::CACHE_KEY);
            Cache::forget(PayNowIntegrationService::CACHE_KEY . '_api_key');
            $settings = PnSetting::first();
            Cache::put(PayNowIntegrationService::CACHE_KEY, $settings, PayNowIntegrationService::CACHE_TTL);
            if ($settings && !empty($settings->api_key)) {
                try {
                    $decryptedApiKey = Crypt::decryptString($settings->api_key);
                    Cache::put(PayNowIntegrationService::CACHE_KEY . '_api_key', $decryptedApiKey, PayNowIntegrationService::CACHE_TTL);
                } catch (\Exception $e) {
                    Log::error('Failed to cache decrypted API key: ' . $e->getMessage());
                }
            }

            $chain = [];
            if ($newEnabled && $originalEnabled === PnSetting::STATUS_DISABLED) {
                PnHistory::create([
                    'type' => PnHistory::TYPE_INFO,
                    'event' => PnHistory::EVENT_APPLICATION_SUBMITTED,
                    'timeline' => true,
                    'message' => 'You enabled PayNow Integration.',
                    'details' => ['store_id' => $validatedData['store_id']],
                    'created_at' => now(),
                ]);

                $chain[] = new EnablePaynowJob();
                $chain[] = new SyncPaynowSettingsJob();
                $chain[] = new ParsePaynowLogsJob();
                $chain[] = new PaynowSyncJob();
            } elseif (!$validatedData['enabled'] && $originalEnabled === PnSetting::STATUS_ENABLED) {
                PnHistory::create([
                    'type' => PnHistory::TYPE_WARNING,
                    'event' => PnHistory::EVENT_WEBSTORE_SUSPENDED,
                    'timeline' => true,
                    'message' => 'You disabled PayNow Integration.',
                    'details' => ['store_id' => $validatedData['store_id']],
                    'created_at' => now(),
                ]);

                $pnSettings->update([
                    'enabled' => PnSetting::STATUS_DISABLED,
                ]);

                //$chain[] = new PaynowDisableJob();
            } elseif ($newEnabled && (
                    $originalStoreId !== $validatedData['store_id'] ||
                    ($originalApiKey && Crypt::decryptString($originalApiKey) !== $validatedData['api_key'])
                )) {
                PnHistory::create([
                    'type' => PnHistory::TYPE_WARNING,
                    'event' => PnHistory::EVENT_APPLICATION_SUBMITTED,
                    'timeline' => true,
                    'message' => 'You updated your PayNow Integration settings.',
                    'details' => ['store_id' => $validatedData['store_id']],
                    'created_at' => now(),
                ]);

                $chain[] = new PaynowDisableJob();
                $chain[] = new EnablePaynowJob();
                $chain[] = new SyncPaynowSettingsJob();
                $chain[] = new ParsePaynowLogsJob();
                $chain[] = new PaynowSyncJob();
            }

            if (!empty($chain)) {
                Bus::chain($chain)
                    ->catch(function ($e) {
                        Log::error('PayNow settings update failed: ' . $e->getMessage());
                    })
                    ->dispatch();
            }

            $newTaxInclusive = $validatedData['tax_mode'] === 1;
            $currentStore = PaynowManagement::getStore();
            $currentTaxInclusive = $currentStore->store_tax_inclusive_pricing ?? false;

            if ($newTaxInclusive !== $currentTaxInclusive) {
                $storeData = [
                    'website_url' => config('app.url'),
                    'store_tax_inclusive_pricing' => $newTaxInclusive,
                ];
                PaynowManagement::updateStoreSettings($storeData);
            }

            return redirect()->route('paynow.index')->with('success', 'PayNow store updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating PayNow settings: ' . $e->getMessage());
            return redirect()->route('paynow.index')->with('error', 'An error occurred while updating PayNow settings: ' . $e->getMessage());
        }
    }

    /**
     * Update or add values to .env file (Not being used for now)
     *
     * @param array $values Key-value pairs to update or add
     * @return bool Success status
     */
    private function updateEnvFile(array $values): bool
    {
        try {
            $envFilePath = base_path('.env');

            if (!file_exists($envFilePath) || !is_writable($envFilePath)) {
                throw new \RuntimeException("File not found or not writable: {$envFilePath}");
            }

            $envFileContent = file_get_contents($envFilePath);
            $allowedKeys = [
                'PAYNOW_ENABLED', 'PAYNOW_TAX_MODE', 'PAYNOW_STORE_ID', 'PAYNOW_API_KEY'
            ];

            $updated = false;

            foreach ($values as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    \Log::warning('Attempt to set unauthorized .env key', ['key' => $key]);
                    throw new \RuntimeException('Unauthorized environment key: ' . $key);
                }

                $stringValue = (string)$value;

                switch ($key) {
                    case 'PAYNOW_ENABLED':
                        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        if ($boolValue === null) {
                            throw new \RuntimeException('Invalid boolean value for PAYNOW_ENABLED');
                        }
                        $escapedValue = $boolValue ? 'true' : 'false';
                        break;

                    case 'PAYNOW_TAX_MODE':
                        if (!in_array($stringValue, ['0', '1'], true)) {
                            throw new \RuntimeException('Invalid PAYNOW_TAX_MODE value, must be 0 or 1');
                        }
                        $escapedValue = '"' . $stringValue . '"';
                        break;

                    case 'PAYNOW_STORE_ID':
                        if (!preg_match('/^[0-9]+$/', $stringValue)) {
                            throw new \RuntimeException('Invalid PAYNOW_STORE_ID format, must contain only digits');
                        }
                        $escapedValue = '"' . $stringValue . '"';
                        break;

                    case 'PAYNOW_API_KEY':
                        $sanitizedValue = preg_replace('/[\r\n\t\\\\\'\"`,;]/', '', $stringValue);

                        if (empty($sanitizedValue) || $sanitizedValue !== $stringValue) {
                            throw new \RuntimeException('Invalid PAYNOW_API_KEY format, contains invalid characters');
                        }

                        $escapedValue = '"' . $sanitizedValue . '"';
                        break;

                    default:
                        throw new \RuntimeException('Unexpected key: ' . $key);
                }

                if (preg_match("/^{$key}=.*$/m", $envFileContent)) {
                    $envFileContent = preg_replace(
                        "/^{$key}=.*$/m",
                        "{$key}={$escapedValue}",
                        $envFileContent
                    );
                } else {
                    $envFileContent = rtrim($envFileContent) . PHP_EOL . "{$key}={$escapedValue}";
                }

                $updated = true;
            }

            if ($updated) {
                file_put_contents($envFilePath, $envFileContent, LOCK_EX);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating .env file: ' . $e->getMessage());
            return false;
        }
    }

    private function wasRegistered(): bool
    {
        $pnSettings = PnSetting::first();
        if (!$pnSettings) {
            Log::info('[PayNow] No PnSetting record found, onboarding required');
            return false;
        }

        return true;
    }

    private function getTimelineEvents(): array
    {
        $timelineEvents = PnHistory::timeline()
            ->get()
            ->map(function (PnHistory $event) {
                $typeMap = [
                    PnHistory::TYPE_SUCCESS => 'success',
                    PnHistory::TYPE_ERROR => 'danger',
                    PnHistory::TYPE_WARNING => 'warning',
                    PnHistory::TYPE_INFO => 'info',
                ];

                return [
                    'date' => $event->created_at->format('Y-m-d'),
                    'type' => $typeMap[$event->type] ?? 'info',
                    'title' => $event->getEventTitle(),
                    'description' => $event->message,
                    'carbon_date' => $event->created_at,
                ];
            })
            ->toArray();

        $now = Carbon::now();

        return collect($timelineEvents)
            ->map(function ($event) use ($now) {
                $date = $event['carbon_date'];

                if ($date->diffInMinutes($now) < 60) {
                    $diff = round($date->diffInMinutes($now));
                    $event['timeAgo'] = "{$diff} " . Str::plural('minute', $diff) . " ago";
                } elseif ($date->diffInHours($now) < 24) {
                    $diff = round($date->diffInHours($now));
                    $event['timeAgo'] = "{$diff} " . Str::plural('hour', $diff) . " ago";
                } elseif ($date->diffInDays($now) < 30) {
                    $diff = round($date->diffInDays($now));
                    $event['timeAgo'] = "{$diff} " . Str::plural('day', $diff) . " ago";
                } else {
                    $diff = round($date->diffInMonths($now));
                    $event['timeAgo'] = "{$diff} " . Str::plural('month', $diff) . " ago";
                }

                return $event;
            })
            ->sortByDesc('carbon_date')
            ->map(function ($event) {
                unset($event['carbon_date']);
                return $event;
            })
            ->values()
            ->all();
    }

    private function getSyncing(): ?array
    {
        $syncData = null;

        $latestSync = PnSyncLog::latest('last_synced_at')->first();

        if ($latestSync) {
            $lastSyncTime = Carbon::parse($latestSync->last_synced_at);
            $minutesSinceLastSync = $lastSyncTime->diffInMinutes(now(), true);

            $syncData = [
                'lastSync' => $lastSyncTime->toDateTimeString(),
                'isRecent' => $minutesSinceLastSync < 15
            ];
        }

        return $syncData;
    }

    private function taxSyncing(): ?array
    {
        $taxSync = null;

        $latestTaxSync = PnVatRate::latest('updated_at')->first();

        if ($latestTaxSync) {
            $lastSyncTime = Carbon::parse($latestTaxSync->updated_at);
            $hoursSinceLastSync = $lastSyncTime->diffInHours(now(), true);

            $taxSync = [
                'lastSync' => $lastSyncTime->toDateTimeString(),
                'isRecent' => $hoursSinceLastSync < 24
            ];
        }

        return $taxSync;
    }

    private function getTaxSyncDiagnostics($taxSync): array
    {
        if (!$taxSync) {
            return [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ];
        }

        return $taxSync['isRecent'] ? [
            'status' => 'recent',
            'badge' => 'success',
            'icon' => 'bx-refresh',
            'message' => '< 24 hours ago',
            'description' => null,
            'lastSync' => $taxSync['lastSync']
        ] : [
            'status' => 'outdated',
            'badge' => 'warning',
            'icon' => 'bx-time',
            'message' => 'Rates Outdated',
            'description' => "Your PayNow tax rates have not been updated in more than 24 hours. <br><a href='https://minestorecms.com/discord'>Contact MineStoreCMS Support</a> if this issue persists.",
            'lastSync' => $taxSync['lastSync']
        ];
    }

    private function initializeDiagnostics(): array
    {
        return [
            'integration' => [
                'status' => 'not_configured',
                'badge' => 'danger',
                'icon' => 'bx-x-circle',
                'message' => 'Not Configured',
                'description' => null
            ],
            'currency' => [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ],
            'sync' => [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ],
            'taxSync' => [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ]
        ];
    }

    private function getIntegrationDiagnostics($payNowStore): array
    {
        if (empty($payNowStore)) {
            return [
                'status' => 'not_configured',
                'badge' => 'danger',
                'icon' => 'bx-x-circle',
                'message' => 'Not Configured',
                'description' => null
            ];
        }

        if (!isset($payNowStore['live_mode'])) {
            return [
                'status' => 'misconfigured',
                'badge' => 'warning',
                'icon' => 'bx-error-circle',
                'message' => 'Misconfigured',
                'description' => 'Store data is incomplete. Missing "live_mode" parameter.'
            ];
        }

        return $payNowStore['live_mode'] ? [
            'status' => 'operational',
            'badge' => 'success',
            'icon' => 'bx-check-circle',
            'message' => 'Operational',
            'description' => null
        ] : [
            'status' => 'inactive',
            'badge' => 'danger',
            'icon' => 'bx-x-circle',
            'message' => 'Inactive',
            'description' => 'Your PayNow Webstore is currently inactive. Please check your PayNow account settings.'
        ];
    }

    private function getCurrencyDiagnostics(string $webstorePrimaryCurrency, $payNowStore): array
    {
        if (empty($payNowStore)) {
            return [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ];
        }

        if (!isset($payNowStore['currency'])) {
            return [
                'status' => 'unknown',
                'badge' => 'warning',
                'icon' => 'bx-error-circle',
                'message' => 'Unknown Currency',
                'description' => 'Unable to determine PayNow store currency.'
            ];
        }

        return mb_strtolower($webstorePrimaryCurrency) === mb_strtolower($payNowStore['currency']) ? [
            'status' => 'match',
            'badge' => 'success',
            'icon' => 'bx-dollar-circle',
            'message' => $webstorePrimaryCurrency,
            'description' => null
        ] : [
            'status' => 'mismatch',
            'badge' => 'danger',
            'icon' => 'bx-x-circle',
            'message' => 'CURRENCY MISMATCH',
            'description' => "Your webstore default currency is set to <strong>{$webstorePrimaryCurrency}</strong> but your PayNow Webstore is set to <strong>" . strtoupper($payNowStore['currency']) . "</strong>."
        ];
    }

    private function getSyncDiagnostics($syncData): array
    {
        if (!$syncData) {
            return [
                'status' => null,
                'badge' => null,
                'icon' => null,
                'message' => null,
                'description' => null
            ];
        }

        return $syncData['isRecent'] ? [
            'status' => 'recent',
            'badge' => 'success',
            'icon' => 'bx-refresh',
            'message' => '< 15 minutes ago',
            'description' => null,
            'lastSync' => $syncData['lastSync']
        ] : [
            'status' => 'failed',
            'badge' => 'danger',
            'icon' => 'bx-x-circle',
            'message' => 'Sync Failed',
            'description' => 'Your PayNow Webstore has not been synced with MineStoreCMS recently. <br><a href="https://minestorecms.com/discord">Contact MineStoreCMS Support</a> if this issue persists.',
            'lastSync' => $syncData['lastSync']
        ];
    }

    private function checkForIssues(array $diagnostics, $payNowStore): array
    {
        $hasIssues = false;
        $issuesList = [];

        $storeExists = !empty($payNowStore) && is_array($payNowStore);

        if (isset($diagnostics['currency']['status']) && $diagnostics['currency']['status'] === 'mismatch') {
            $hasIssues = true;
            $issuesList[] = 'Currency mismatch detected';
        }

        if (isset($diagnostics['currency']['status']) && $diagnostics['currency']['status'] === 'unknown') {
            $hasIssues = true;
            $issuesList[] = 'Unknown store currency';
        }

        if (isset($diagnostics['sync']['status']) && $diagnostics['sync']['status'] === 'failed') {
            $hasIssues = true;
            $issuesList[] = 'Sync failure detected';
        }

        if (isset($diagnostics['taxSync']['status']) && $diagnostics['taxSync']['status'] === 'outdated') {
            $hasIssues = true;
            $issuesList[] = 'Tax rates sync is outdated';
        }

        if ($storeExists && isset($diagnostics['integration']['status']) && $diagnostics['integration']['status'] === 'inactive') {
            $hasIssues = true;
            $issuesList[] = 'PayNow store is inactive';
        }

        if (isset($diagnostics['integration']['status']) && $diagnostics['integration']['status'] === 'misconfigured') {
            $hasIssues = true;
            $issuesList[] = 'PayNow store is misconfigured';
        }

        return [
            'hasIssues' => $hasIssues,
            'issuesList' => $issuesList
        ];
    }

    private function isPayNowConfigured(): bool
    {
        $settings = PnSetting::first();

        if (!$settings) {
            Log::info('[PayNow] No PnSetting record found, onboarding required');
            return false;
        }

        $isConfigured = $settings->enabled === PnSetting::STATUS_ENABLED &&
            !empty($settings->store_id) &&
            !empty($settings->api_key);

        Log::info('[PayNow] Configuration check', [
            'enabled' => $settings->enabled,
            'store_id' => $settings->store_id ? 'present' : 'missing',
            'api_key' => $settings->api_key ? 'present' : 'missing',
            'is_configured' => $isConfigured,
        ]);

        return $isConfigured;
    }
}
