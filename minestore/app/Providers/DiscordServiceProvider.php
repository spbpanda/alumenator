<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use SocialiteProviders\Discord\Provider as DiscordProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class DiscordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        config([
            'services.discord' => [
                'client_id' => null,
                'client_secret' => null,
                'redirect' => config('app.url') . '/api/auth/discord/callback',
            ]
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            try {
                if (Schema::hasTable('settings') && Schema::hasColumns('settings', ['discord_client_id', 'discord_client_secret'])) {
                    $settings = Setting::select(['discord_client_id', 'discord_client_secret'])->first();

                    if ($settings) {
                        config([
                            'services.discord.client_id' => $settings->discord_client_id,
                            'services.discord.client_secret' => $settings->discord_client_secret,
                        ]);
                    }
                } else {
                    \Log::warning('Table `settings` or columns `discord_client_id`, `discord_client_secret` not found.');
                }
            } catch (\Exception $e) {
                \Log::error('Database connection error: ' . $e->getMessage());
            }
        });

        $this->app['events']->listen(SocialiteWasCalled::class, function (SocialiteWasCalled $socialiteWasCalled) {
            $socialiteWasCalled->extendSocialite('discord', DiscordProvider::class);
        });
    }
}
