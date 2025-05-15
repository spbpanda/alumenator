<?php

namespace App\Listeners;

use App\Events\PaymentPaid;
use App\Events\UpdateAvailable;
use App\Http\Controllers\Admin\UsersController;
use App\Models\Admin;
use App\Notifications\NewPayment;
use App\Notifications\NewUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNewUpdateNotification
{
    private Collection $admins;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->admins = Admin::all();
    }

    /**
     * Handle the event.
     *
     * @param UpdateAvailable $event
     * @return void
     */
    public function handle(UpdateAvailable $event)
    {
        Notification::send($this->admins, new NewUpdate($event->data));
    }
}
