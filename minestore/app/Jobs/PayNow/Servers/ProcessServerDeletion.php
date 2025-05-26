<?php

namespace App\Jobs\PayNow\Servers;

use App\Integrations\PayNow\Management;
use App\Models\PnServerReference;
use App\Models\Server;
use App\Observers\ServerObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ProcessServerDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected PnServerReference $pnServer;
    protected Management $management;

    /**
     * Create a new job instance.
     */
    public function __construct(Server $server, PnServerReference $pnServer, Management $management)
    {
        $this->server = $server;
        $this->pnServer = $pnServer;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $observer = app(ServerObserver::class);
            $observer->deleteServer($this->server, $this->pnServer, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessServerDeletion failed', [
                'server_id' => $this->server->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
