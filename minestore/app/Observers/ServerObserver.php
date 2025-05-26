<?php

namespace App\Observers;

use App;
use App\Integrations\PayNow\Management;
use App\Jobs\PayNow\Servers\ProcessServerCreation;
use App\Jobs\PayNow\Servers\ProcessServerDeletion;
use App\Jobs\PayNow\Servers\ProcessServerUpdate;
use App\Models\PnServerReference;
use App\Models\Server;
use App\Services\PayNowIntegrationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServerObserver
{
    /**
     * Prepare Game Server data for PayNow API
     *
     * @param Server $server
     * @return array
     */
    public function preparePayNowData(Server $server): array
    {
        return [
            'name' => $server->name . '-' . Str::random(4),
            'enabled' => (bool)!$server->deleted,
        ];
    }

    /**
     * Sync Game Server with PayNow
     *
     * @param Server $server
     * @return void
     */
    protected function syncWithPayNow(Server $server): void
    {
        try {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled() || !$paynowService->validateRequest()) {
                return;
            }

            $management = App::make(Management::class);

            $data = $this->preparePayNowData($server);
            $pnServer = PnServerReference::where('internal_server_id', $server->id)->first();

            if (!$pnServer) {
                ProcessServerCreation::dispatch($server, $data, $management);
            } else {
                ProcessServerUpdate::dispatch($server, $data, $management, $pnServer);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to sync server with PayNow: ' . $e->getMessage());
        }
    }

    /**
     * Create server in PayNow
     *
     * @param Server $server
     * @param array $data
     * @param Management $management
     * @return void
     */
    public function createPayNowServer(Server $server, array $data, Management $management): void
    {
        $externalId = $management->createServer($data);

        if ($externalId) {
            Log::info('[PayNow] ServerObserver: server created successfully', [
                'server_id' => $server->id,
                'external_server_id' => $externalId,
            ]);

            // Save the external server ID to the database
            PnServerReference::create([
                'internal_server_id' => $server->id,
                'external_server_id' => $externalId,
            ]);
        } else {
            Log::error('[PayNow] ServerObserver: failed to create server', [
                'server_id' => $server->id,
            ]);
        }
    }

    /**
     * Update server in PayNow
     *
     * @param Server $server
     * @param array $data
     * @param Management $management
     * @param PnServerReference $pnServer
     * @return void
     */
    protected function updatePayNowServer(Server $server, array $data, Management $management, PnServerReference $pnServer): void
    {
        $externalId = $pnServer->external_server_id;

        if ($management->updateServer($externalId, $data)) {
            Log::info('[PayNow] ServerObserver: server updated successfully', [
                'server_id' => $server->id,
                'external_server_id' => $externalId,
            ]);
        } else {
            Log::error('[PayNow] ServerObserver: failed to update server', [
                'server_id' => $server->id,
                'external_server_id' => $externalId,
            ]);
        }
    }

    /**
     * Delete server in PayNow
     *
     * @param Server $server
     * @param PnServerReference $pnServer
     * @param Management $management
     *
     * @return void
     */
    public function deletePayNowServer(Server $server, PnServerReference $pnServer, Management $management): void
    {
        $result = $management->deleteServer($pnServer);

        if ($result) {
            Log::info('[PayNow] ServerObserver: server deleted successfully', [
                'server_id' => $server->id,
                'external_server_id' => $pnServer->external_server_id,
            ]);

            $pnServer->delete();
        } else {
            Log::error('[PayNow] ServerObserver: failed to delete server', [
                'server_id' => $server->id,
                'external_server_id' => $pnServer->external_server_id,
            ]);
        }
    }

    /**
     * Handle the Server "created" event.
     */
    public function created(Server $server): void
    {
        Log::info('[PayNow] ServerObserver: created', [
            'name' => $server->name,
            'server_id' => $server->id
        ]);

        $this->syncWithPayNow($server);
    }

    /**
     * Handle the Server "updated" event.
     */
    public function updated(Server $server): void
    {
        Log::info('[PayNow] ServerObserver: updated', [
            'name' => $server->name,
            'server_id' => $server->id
        ]);

        $this->syncWithPayNow($server);

        if ($server->deleted) {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled()) {
                return;
            }

            $management = App::make(Management::class);
            $pnServer = PnServerReference::where('internal_server_id', $server->id)->first();

            if ($pnServer) {
                ProcessServerDeletion::dispatch($server, $pnServer, $management);
            }
        }
    }

    /**
     * Handle the Server "deleted" event.
     */
    public function deleted(Server $server): void
    {
        try {
            $paynowService = app()->make(PayNowIntegrationService::class);

            if (!$paynowService->isPaymentMethodEnabled()) {
                return;
            }

            $management = App::make(Management::class);
            $pnServer = PnServerReference::where('internal_server_id', $server->id)->first();

            if ($pnServer) {
                ProcessServerDeletion::dispatch($server, $pnServer, $management);
            }
        } catch (\Exception $e) {
            Log::error('[PayNow] ServerObserver delete exception', [
                'server_id' => $server->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Server "restored" event.
     */
    public function restored(Server $server): void
    {
        //
    }

    /**
     * Handle the Server "force deleted" event.
     */
    public function forceDeleted(Server $server): void
    {
        //
    }
}
