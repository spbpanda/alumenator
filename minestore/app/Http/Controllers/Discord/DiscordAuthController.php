<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;

class DiscordAuthController extends Controller
{
    public function redirect()
    {
        $settings = Setting::select(['discord_bot_enabled', 'discord_client_id', 'discord_client_secret'])->first();
        if (!$settings || !$settings->discord_bot_enabled || !$settings->discord_client_id || !$settings->discord_client_secret) {
            return response()->json([
                'success' => false,
                'error' => 'Discord Integration is disabled or not configured.'
            ], 400);
        }

        try {
            $url = Socialite::driver('discord')
                ->scopes(['identify'])
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate auth URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtain the user information from Discord.
     *
     */
    public function callback(Request $request)
    {
        try {
            $discordUser = Socialite::driver('discord')
                ->stateless()
                ->user();

            return view('pages.discord.callback', [
                'data' => [
                    'success' => true,
                    'discord_username' => $discordUser->getNickname(),
                    'discord_id' => $discordUser->getId()
                ]
            ]);

        } catch (Exception $e) {
            return view('pages.discord.callback', [
                'data' => [
                    'success' => false,
                    'error' => true,
                    'message' => 'Failed to link Discord account: ' . $e->getMessage()
                ]
            ]);
        }
    }
}
