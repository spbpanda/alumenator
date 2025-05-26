<?php

namespace App\Jobs\PayNow\Servers;

use App\Observers\ServerObserver;
use App\Models\Server;
use App\Integrations\PayNow\Management;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessServerCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Server $server;
    protected array $data;
    protected Management $management;

    /**
     * Create a new job instance.
     */
    public function __construct(Server $server, array $data, Management $management)
    {
        $this->server = $server;
        $this->data = $data;
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
            $observer->createPayNowServer($this->server, $this->data, $this->management);
        } catch (\Exception $e) {
            Log::error('[PayNow] ProcessServerCreation failed', [
                'server_id' => $this->server->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
