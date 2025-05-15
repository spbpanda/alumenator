<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscordBotStatusService
{
    public function shouldRunService(): bool
    {
        try {
            $botEnabled = Setting::value('discord_bot_enabled');
            return (bool) $botEnabled;
        } catch (\Exception $e) {
            Log::error('Error while checking if Discord bot should run: ' . $e->getMessage());
            return false;
        }
    }
}
