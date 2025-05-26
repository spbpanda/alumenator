<?php

namespace App\Listeners;

use App\Http\Controllers\Admin\UsersController;
use App\Notifications\NewAlert;
use Illuminate\Support\Facades\Notification;

class SendNewAlertNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->admins = UsersController::adminsWithRule('payments', 'read');
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Notification::send($this->admins, new NewAlert());
    }
}
