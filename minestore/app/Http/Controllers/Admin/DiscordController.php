<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscordRequest;
use App\Models\SecurityLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Process;
use Log;

class DiscordController extends Controller
{
    /**
     * Display the Discord settings page.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * Redirects to the admin page if the user does not have read permissions for settings,
     * otherwise returns the view for the Discord settings page with the current settings.
     */
    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $settings = Setting::select(['discord_guild_id', 'discord_url', 'discord_bot_token', 'discord_bot_enabled', 'webhook_url', 'discord_client_id', 'discord_client_secret'])->first();

        return view('admin.discord.index', compact('settings'));
    }

    /**
     * Store the Discord settings.
     *
     * @param StoreDiscordRequest $request The request object containing the new Discord settings.
     * @return \Illuminate\Http\RedirectResponse Redirects to the Discord settings page with a success message.
     */
    public function store(StoreDiscordRequest $request): \Illuminate\Http\RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $settings = Setting::firstOrFail();

        $new_discord_bot_enabled = $request->discord_bot_enabled;

        Log::info('Updating Discord settings...');

        if ($settings->discord_bot_enabled !== $new_discord_bot_enabled) {
            switch ($new_discord_bot_enabled) {
                case 0:
                    Log::info('Disabling Discord bot...');
                    Process::run('sudo systemctl stop minestore_discord.service');
                    break;
                case 1:
                    Log::info('Enabling Discord bot...');
                    Process::run('sudo systemctl start minestore_discord.service');
                    break;
            }
        }

        $updated = $settings->update([
            'discord_guild_id' => $request->discord_guild_id,
            'discord_url' => $request->discord_url,
            'discord_bot_token' => $request->discord_bot_token,
            'discord_bot_enabled' => $new_discord_bot_enabled,
            'webhook_url' => $request->webhook_url,
            'discord_client_id' => $request->discord_client_id,
            'discord_client_secret' => $request->discord_client_secret,
        ]);

        if ($updated) {
            Log::info('Discord settings updated successfully');
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['discord'],
            'extra' => __('edited discord settings'),
        ]);

        return redirect()->route('discord.index')->with('success', __('Discord settings updated successfully!'));
    }
}
