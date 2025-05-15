<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DiscordBotStatusService;

class DiscordBotServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DiscordBotStatusService::class, function () {
            return new DiscordBotStatusService();
        });
    }

    public function boot()
    {
        //
    }
}
