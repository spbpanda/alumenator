<?php

namespace App\Providers;

use App\Events\ChargebackCreated;
use App\Events\MonthlyReportGenerated;
use App\Events\PaymentPaid;
use App\Events\ThemeInstalled;
use App\Events\UpdateAvailable;
use App\Listeners\SendMetricsData;
use App\Listeners\SendMonthlyReportNotification;
use App\Listeners\SendNewChargebackNotification;
use App\Listeners\SendNewPaymentNotification;
use App\Listeners\SendNewUpdateNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PaymentPaid::class => [
            SendNewPaymentNotification::class
        ],
        ChargebackCreated::class => [
            SendNewChargebackNotification::class
        ],
        UpdateAvailable::class => [
            SendNewUpdateNotification::class
        ],
        MonthlyReportGenerated::class => [
            SendMonthlyReportNotification::class
        ],
        ThemeInstalled::class => [
            SendMetricsData::class,
        ],
        SocialiteWasCalled::class => [
            'SocialiteProviders\\Discord\\DiscordExtendSocialite@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
