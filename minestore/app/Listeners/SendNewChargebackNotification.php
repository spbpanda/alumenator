<?php

namespace App\Listeners;

use App\Events\ChargebackCreated;
use App\Events\PaymentPaid;
use App\Http\Controllers\Admin\UsersController;
use App\Notifications\NewChargeback;
use App\Notifications\NewPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNewChargebackNotification
{
    private Collection $admins;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->admins = UsersController::adminsWithRule('fraud', 'read');
    }

    /**
     * Handle the event.
     *
     * @param ChargebackCreated $event
     * @return void
     */
    public function handle(ChargebackCreated $event)
    {
        Notification::send($this->admins, new NewChargeback($event->chargeback));
    }
}
