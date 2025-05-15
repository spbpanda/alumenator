<?php

namespace App\Listeners;

use App\Events\MonthlyReportGenerated;
use App\Events\PaymentPaid;
use App\Http\Controllers\Admin\UsersController;
use App\Notifications\MonthlyReport;
use App\Notifications\NewPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMonthlyReportNotification
{

    private Collection $admins;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->admins = UsersController::adminsWithRule('statistics', 'read');
    }

    /**
     * Handle the event.
     *
     * @param MonthlyReportGenerated $event
     * @return void
     */
    public function handle(MonthlyReportGenerated $event)
    {
        Notification::send($this->admins, new MonthlyReport($event->month, $event->revenue));
    }
}
