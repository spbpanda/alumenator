<?php

namespace App\Observers;

use App;
use App\Integrations\PayNow\Management;
use App\Jobs\PayNow\Customers\ProcessCustomerCreation;
use App\Jobs\PayNow\Customers\ProcessCustomerUpdate;
use App\Models\PnCustomerReference;
use App\Models\User;
use App\Services\PayNowIntegrationService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Prepare Customer data for PayNow API
     *
     * @param User $user
     * @return array
     */
    public function preparePayNowData(User $user): array
    {
        return [
            'name' => $user->username,
            'uuid' => $user->uuid ?? null,
            'metadata' => [
                'minestore_user_id' => (string)$user->id,
            ]
        ];
    }

    /**
     * Sync User with PayNow
     *
     * @param User $user
     * @return void
     */
    protected function syncWithPayNow(User $user): void
    {
        try {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled() || !$paynowService->validateRequest()) {
                return;
            }

            $management = App::make(Management::class);

            $data = $this->preparePayNowData($user);
            $pnUser = PnCustomerReference::where('internal_user_id', $user->id)->first();

            if (!$pnUser) {
                ProcessCustomerCreation::dispatch($user, $data, $management);
            } else {
                ProcessCustomerUpdate::dispatch($user, $data, $management, $pnUser);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to sync user with PayNow: ' . $e->getMessage());
        }
    }

    public function createPayNowCustomer(User $user, array $data, Management $management): void
    {
        $customer = $management->createCustomer($data);

        if ($customer) {
            Log::info('[PayNow] UserObserver: customer created successfully', [
                'user_id' => $user->id,
                'external_id' => $customer['id'],
            ]);

            PnCustomerReference::create([
                'internal_user_id' => $user->id,
                'external_user_id' => $customer['id'],
            ]);
        } else {
            Log::error('[PayNow] UserObserver: failed to create customer', [
                'user_id' => $user->id,
            ]);
        }
    }

    public function updatePayNowCustomer(User $user, array $data, Management $management, PnCustomerReference $pnUser): void
    {
        $externalId = $pnUser->external_user_id;

        if ($management->updateCustomer($externalId, $data)) {
            Log::info('[PayNow] UserObserver: customer updated successfully', [
                'user_id' => $user->id,
                'external_id' => $externalId,
            ]);
        } else {
            Log::error('[PayNow] UserObserver: failed to update customer', [
                'user_id' => $user->id,
                'external_id' => $externalId,
            ]);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Log::info('[PayNow] UserObserver: created', [
            'user_id' => $user->id,
        ]);

        $this->syncWithPayNow($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        Log::info('[PayNow] UserObserver: updated', [
            'user_id' => $user->id,
        ]);

        $this->syncWithPayNow($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
