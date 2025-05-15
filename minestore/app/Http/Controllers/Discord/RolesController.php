<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;
use App\Models\DiscordRoleQueue;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    public function handle(array $discordData): void
    {
        $action = $discordData['action'];

        if (!isset($discordData['discord_id']) || !isset($discordData['role_id']) || !isset($discordData['internal_role_id']) || !isset($discordData['user_id']) || !isset($discordData['payment_id']) || !is_string($discordData['discord_id']) || !is_numeric($discordData['role_id']) || !is_numeric($discordData['internal_role_id']) || !is_numeric($discordData['user_id']) || !is_numeric($discordData['payment_id'])) {
            Log::error('Invalid data received', ['data' => $discordData]);
            return;
        }

        switch($action) {
            case DiscordRoleQueue::GIVE_ROLE:
                $this->giveRole($discordData);
                break;
            case DiscordRoleQueue::REMOVE_ROLE:
                $this->removeRole($discordData);
                break;
        }
    }

    private function giveRole(array $discordData): void
    {
        try {
            DiscordRoleQueue::create([
                'discord_id' => $discordData['discord_id'],
                'action' => DiscordRoleQueue::GIVE_ROLE,
                'role_id' => $discordData['role_id'],
                'internal_role_id' => $discordData['internal_role_id'],
                'user_id' => $discordData['user_id'],
                'payment_id' => $discordData['payment_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to give role: ' . $e->getMessage(), ['data' => $discordData]);
        }
    }

    private function removeRole(array $discordData): void
    {
        try {
            DiscordRoleQueue::create([
                'discord_id' => $discordData['discord_id'],
                'action' => DiscordRoleQueue::REMOVE_ROLE,
                'role_id' => $discordData['role_id'],
                'internal_role_id' => $discordData['internal_role_id'],
                'user_id' => $discordData['user_id'],
                'payment_id' => $discordData['payment_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to remove role: ' . $e->getMessage(), ['data' => $discordData]);
        }
    }
}
