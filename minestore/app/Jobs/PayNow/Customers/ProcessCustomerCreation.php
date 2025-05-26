<?php

namespace App\Jobs\PayNow\Customers;

use App\Integrations\PayNow\Management;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCustomerCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected array $data;
    protected Management $management;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, array $data, Management $management)
    {
        $this->user = $user;
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
            $observer = app(UserObserver::class);
            $observer->createPayNowCustomer($this->user, $this->data, $this->management);
        } catch (\Exception $e) {
            \Log::error('[PayNow] ProcessCustomerCreation failed', [
                'user_id' => $this->user->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
