<?php

namespace App\Console\Commands;

use App\Events\AlertReceived;
use App\Facades\PaynowManagement;
use App\Models\PnAlert;
use App\Models\PnHistory;
use App\Services\PayNowIntegrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class ParsePayNowLogs extends Command
{
    protected $signature = 'paynow:parse-logs';
    protected $description = 'Fetch and parse PayNow API data every 5 minutes, creating new PnHistory events for changes';

    public function handle()
    {
        $paynowService = App::make(PayNowIntegrationService::class);
        if (!$paynowService->isPaymentMethodEnabled()) {
            $this->info('PayNow is disabled. Skipping synchronization.');
            return;
        }

        try {
            $onboardingStatus = PaynowManagement::getOnboarding();
            $storeData = PaynowManagement::getStore();
            $billingStatus = PaynowManagement::getBillingStatus();

            $cacheKey = 'paynow_store_state';
            $lastState = Cache::get($cacheKey, []);

            $newState = [
                'onboarding' => $onboardingStatus,
                'store' => $storeData,
                'billing' => $billingStatus,
            ];

            $this->processOnboardingStatusChanges($onboardingStatus, $lastState['onboarding'] ?? [], $billingStatus, $lastState['billing'] ?? []);
            $this->processStoreDataChanges($storeData, $lastState['store'] ?? []);

            Cache::put($cacheKey, $newState, now()->addMinutes(5));

            $this->processAlerts();

            $this->info('PayNow logs parsed successfully.');
        } catch (\Exception $e) {
            $this->error("Failed to parse PayNow logs: {$e->getMessage()}");
        }
    }

    protected function processOnboardingStatusChanges($current, $last, $billingStatus, $lastBillingStatus)
    {
        if (empty($current)) {
            return;
        }

        // Initialize last state if empty
        $last = $last ?: [
            'status' => '',
            'payout_onboarding_completed' => false,
            'kyc_completed' => false,
            'requires_action_text' => '',
            'decline_reason' => '',
            'kyc_required' => false,
        ];

        // Check for onboarding completion
        if ($current['payout_onboarding_completed'] && !$last['payout_onboarding_completed']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_PAYOUT_ONBOARDING_COMPLETED,
                    'details->payout_onboarding_completed' => true,
                ],
                [
                    'type' => PnHistory::TYPE_SUCCESS,
                    'timeline' => true,
                    'message' => "You successfully completed payout onboarding. Now you can withdraw your earnings from <a href='https://dashboard.paynow.gg/account/wallet'>PayNow Wallet</a>.",
                    'details' => ['payout_onboarding_completed' => true],
                    'created_at' => now(),
                ]
            );
        }

        // Check if payout_onboarding_completed is false
        if (!$current['payout_onboarding_completed'] && $last['payout_onboarding_completed']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_APPLICATION_SUBMITTED,
                    'details->payout_onboarding_completed' => false,
                ],
                [
                    'type' => PnHistory::TYPE_INFO,
                    'timeline' => true,
                    'message' => "You need to complete payout onboarding to withdraw your earning. Please, <a href='https://dashboard.paynow.gg/account'>apply for payout onboarding</a>.",
                    'details' => ['payout_onboarding_completed' => false],
                    'created_at' => now(),
                ]
            );
        }

        // Check if KYC required status has changed
        if ($current['kyc_required'] && !$last['kyc_required']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_KYC_REQUIRED,
                    'details->kyc_required' => true,
                ],
                [
                    'type' => PnHistory::TYPE_WARNING,
                    'timeline' => true,
                    'message' => 'You need to complete KYC verification to start using PayNow. Please, <a href="https://dashboard.paynow.gg/account">apply for KYC</a>.',
                    'details' => ['kyc_required' => true],
                    'created_at' => now(),
                ]
            );
        }

        // Check for KYC completion
        if ($current['kyc_completed'] && !$last['kyc_completed']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_KYC_COMPLETED,
                    'details->kyc_completed' => true,
                ],
                [
                    'type' => PnHistory::TYPE_SUCCESS,
                    'timeline' => true,
                    'message' => 'Congratulations! Your KYC verification has been completed successfully.',
                    'details' => ['kyc_completed' => true],
                    'created_at' => now(),
                ]
            );
        }

        // Check for action required
        if ($current['status'] === 'requires_action' && $current['requires_action_text'] !== ($last['requires_action_text'] ?? '')) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_ACTION_REQUIRED,
                    'details->requires_action_text' => $current['requires_action_text'],
                ],
                [
                    'type' => PnHistory::TYPE_WARNING,
                    'timeline' => true,
                    'message' => 'Action required: ' . $current['requires_action_text'],
                    'details' => ['requires_action_text' => $current['requires_action_text']],
                    'created_at' => now(),
                ]
            );
        }

        // Check for decline reason
        if ($current['status'] === 'declined' && $current['decline_reason'] !== ($last['decline_reason'] ?? '')) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_APPLICATION_REJECTED,
                    'details->decline_reason' => $current['decline_reason'],
                ],
                [
                    'type' => PnHistory::TYPE_ERROR,
                    'timeline' => true,
                    'message' => 'Your application has been declined: ' . $current['decline_reason'] . '. Please, <a href="https://discord.gg/paynow">contact PayNow support</a>.',
                    'details' => ['decline_reason' => $current['decline_reason']],
                    'created_at' => now(),
                ]
            );
        }

        // Check if store is locked or unlocked
        if ($billingStatus && isset($billingStatus['is_locked'])) {
            $lastBillingLocked = $lastBillingStatus['is_locked'] ?? false;
            $lastLockReason = $lastBillingStatus['lock_reason'] ?? '';
            $currentLockReason = $billingStatus['lock_reason'] ?? 'Unknown reason';

            // Check if store was locked
            if (($billingStatus['is_locked'] && !$lastBillingLocked) ||
                ($billingStatus['is_locked'] && $lastBillingLocked && $currentLockReason !== $lastLockReason)) {

                PnHistory::updateOrCreate(
                    [
                        'event' => PnHistory::EVENT_WEBSTORE_SUSPENDED,
                        'details->lock_reason' => $currentLockReason,
                    ],
                    [
                        'type' => PnHistory::TYPE_ERROR,
                        'timeline' => true,
                        'message' => 'Your store has been locked: ' . $currentLockReason . '. Please <a href="https://discord.gg/paynow">contact PayNow support</a>.',
                        'details' => [
                            'is_locked' => true,
                            'lock_reason' => $currentLockReason
                        ],
                        'created_at' => now(),
                    ]
                );
            }

            // Check if store was unlocked
            if (!$billingStatus['is_locked'] && $lastBillingLocked) {
                PnHistory::updateOrCreate(
                    [
                        'event' => PnHistory::EVENT_WEBSTORE_RESTORED,
                        'details->is_locked' => false,
                    ],
                    [
                        'type' => PnHistory::TYPE_SUCCESS,
                        'timeline' => true,
                        'message' => "Your store has been enabled and is now operational.",
                        'details' => ['is_locked' => false],
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    protected function processStoreDataChanges($current, $last)
    {
        if (empty($current)) {
            return;
        }

        $last = $last ?: [
            'live_mode' => false,
            'onboarding_completed_at' => null,
        ];

        // Check if live_mode is disabled
        if ($last['live_mode'] && !$current['live_mode']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_WEBSTORE_SUSPENDED,
                    'details->live_mode' => false,
                ],
                [
                    'type' => PnHistory::TYPE_ERROR,
                    'timeline' => true,
                    'message' => "Your store has been suspended due to suspicious activity. Please <a href='https://discord.gg/paynow'>contact PayNow support</a>.",
                    'details' => ['live_mode' => false],
                    'created_at' => now(),
                ]
            );
        }

        // Check if live_mode is re-enabled
        if (!$last['live_mode'] && $current['live_mode']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_WEBSTORE_RESTORED,
                    'details->live_mode' => true,
                ],
                [
                    'type' => PnHistory::TYPE_SUCCESS,
                    'timeline' => true,
                    'message' => "Your store has been enabled and is now operational.",
                    'details' => ['live_mode' => true],
                    'created_at' => now(),
                ]
            );
        }

        // Check for onboarding completion
        if ($current['onboarding_completed_at'] && $current['onboarding_completed_at'] !== $last['onboarding_completed_at']) {
            PnHistory::updateOrCreate(
                [
                    'event' => PnHistory::EVENT_ONBOARDING_COMPLETED,
                    'details->onboarding_completed_at' => $current['onboarding_completed_at'],
                ],
                [
                    'type' => PnHistory::TYPE_SUCCESS,
                    'timeline' => true,
                    'message' => 'Your application has been approved. You can now start using PayNow for your store.',
                    'details' => ['onboarding_completed_at' => $current['onboarding_completed_at']],
                    'created_at' => $current['onboarding_completed_at'],
                ]
            );
        }
    }

    protected function processAlerts(): void
    {
        $alerts = PaynowManagement::getAlerts();
        if (empty($alerts)) {
            $this->info('No alerts to process.');
            return;
        }

        foreach ($alerts as $alert) {
            $this->info("Processing alert: {$alert['id']}");
            $pnAlert = PnAlert::updateOrCreate(
                ['alert_id' => $alert['id']],
                [
                    'store_id' => $alert['store_id'],
                    'entity_id' => $alert['entity_id'],
                    'status' => $alert['status'],
                    'type' => $alert['type'],
                    'custom_title' => $alert['custom_title'],
                    'custom_message' => $alert['custom_message'],
                    'action_required_at' => $alert['action_required_at'],
                    'action_link' => $alert['action_link'],
                    'store_visible' => $alert['store_visible'],
                    'admin_visible' => $alert['admin_visible'],
                    'resolved_by' => $alert['resolved_by'],
                    'created_at' => $alert['created_at'],
                    'updated_at' => $alert['updated_at'],
                    'expires_at' => $alert['expires_at'],
                    'resolved_at' => $alert['resolved_at'],
                ]
            );

            if ($pnAlert->wasRecentlyCreated) {
                $this->info("New alert created: {$pnAlert->alert_id}");
                event(new AlertReceived());
            }
        }

        $this->info('Alerts processed successfully.');
    }
}
