<?php

namespace App\Listeners;

use App\Events\PaymentPaid;
use App\Http\Controllers\Admin\UsersController;
use App\Notifications\NewPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNewPaymentNotification
{

    private Collection $admins;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->admins = UsersController::adminsWithRule('payments', 'read');
    }

    /**
     * Handle the event.
     *
     * @param PaymentPaid $event
     * @return void
     */
    public function handle(PaymentPaid $event)
    {
        Notification::send($this->admins, new NewPayment($event->payment));
    }
}
