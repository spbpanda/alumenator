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

class ProcessServerUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected array $data;
    protected PnServerReference $pnServer;
    protected Management $management;

    /**
     * Create a new job instance.
     */
    public function __construct(Server $server, array $data, PnServerReference $pnServer, Management $management)
    {
        $this->server = $server;
        $this->data = $data;
        $this->pnServer = $pnServer;
        $this->management = $management;
        $this->onQueue('paynow');
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            $observer = app(ServerObserver::class);
            $observer->updateServer($this->server, $this->data, $this->pnServer, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessServerUpdate failed', [
                'server_id' => $this->server->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
