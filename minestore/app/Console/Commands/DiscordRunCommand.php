<?php

namespace App\Console\Commands;

use App\Models\DiscordRole;
use App\Models\DiscordRoleQueue;
use App\Models\Setting;
use Discord\Parts\User\Activity;
use Discord\WebSockets\Intents;
use Illuminate\Console\Command;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Role;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Discord\WebSockets\Event;
use React\EventLoop\Loop;
use Illuminate\Support\Facades\Log;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DiscordRunCommand extends Command
{
    protected $signature = 'discord:run';
    protected $description = 'Start official Discord bot for MineStoreCMS Role Syncing';

    protected Discord $discord;
    protected LoopInterface $loop;
    protected $lastCheckTime;
    protected int $checkInterval = 10; // seconds

    public function handle()
    {
        $settings = Setting::select(['discord_guild_id', 'discord_bot_token', 'discord_bot_enabled', 'discord_client_id', 'discord_client_secret'])->first();

        if (!$settings->discord_bot_enabled) {
            $this->error('Discord bot is not enabled');
            return CommandAlias::FAILURE;
        }

        if (!$settings->discord_bot_token || !$settings->discord_guild_id || !$settings->discord_client_id || !$settings->discord_client_secret) {
            $this->error('Discord bot token, guild ID, client ID or client secret is not set');
            return CommandAlias::FAILURE;
        }

        $this->info('Starting Discord bot...');

        try {
            $this->loop = Loop::get();
            $this->lastCheckTime = time();

            $this->discord = new Discord([
                'token' => $settings->discord_bot_token,
                'intents' => [
                    Intents::GUILDS, Intents::GUILD_MESSAGES, Intents::GUILD_MEMBERS
                ],
                'loop' => $this->loop
            ]);

            $this->discord->on('init', function (Discord $discord) use ($settings) {
                $this->info(sprintf(
                    '[%s] Logged in as %s',
                    now()->format('Y-m-d H:i:s'),
                    $discord->user->username
                ));

                $activity = new Activity($discord, [
                    'name' => 'MineStoreCMS.com',
                    'type' => Activity::TYPE_STREAMING,
                    'url' => 'https://minestorecms.com/'
                ]);

                $discord->updatePresence($activity, false, 'online', false);

                // Reveal message about count of members in the Discord server
                $discord->guilds->fetch($settings->discord_guild_id)->then(
                    function ($guild) {
                        $guild->members->fetch()->then(
                            function ($members) {
                                $this->info(sprintf(
                                    '[%s] Discord Server Members Count: %d',
                                    now()->format('Y-m-d H:i:s'),
                                    $members->count()
                                ));
                            },
                            function ($error) {
                                $this->error(sprintf(
                                    '[%s] Failed to fetch members: %s',
                                    now()->format('Y-m-d H:i:s'),
                                    $error->getMessage()
                                ));
                            }
                        );
                    },
                    function ($error) {
                        $this->error(sprintf(
                            '[%s] Failed to fetch guild: %s',
                            now()->format('Y-m-d H:i:s'),
                            $error->getMessage()
                        ));
                    }
                );

                $this->loop->addPeriodicTimer(1, function () use ($discord, $settings) {
                    $currentTime = time();

                    if ($currentTime - $this->lastCheckTime >= $this->checkInterval) {
                        $pendingCount = DiscordRoleQueue::where('processed', false)
                            ->where('attempts', '<', 3)
                            ->count();

                        if ($pendingCount > 0) {
                            $this->line(sprintf(
                                '[%s] Processing %d pending role assignments...',
                                now()->format('Y-m-d H:i:s'),
                                $pendingCount
                            ));

                            $this->processRoleQueue($discord, $settings);
                        }

                        $this->lastCheckTime = $currentTime;
                    }
                });

                // Sync roles with the database right after the bot starts
                $this->syncRoles($settings);

                // Sync roles every 5 minutes
                $this->loop->addPeriodicTimer(300, function () use ($discord, $settings) {
                    $this->syncRoles($settings);
                });
            });

            // Message handler
            $this->discord->on(Event::MESSAGE_CREATE, function ($message) {
                if ($message->author->bot) {
                    return;
                }
                $this->handleCommands($message);
            });

            $this->discord->run();

        } catch (\Exception $e) {
            $this->error(sprintf(
                '[%s] Error starting bot: %s',
                now()->format('Y-m-d H:i:s'),
                $e->getMessage()
            ));
            Log::error("Discord bot error: " . $e->getMessage());
            return CommandAlias::FAILURE;
        }
    }

    protected function handleCommands($message)
    {
        $commands = [
            '!ping' => fn() => $message->reply('Pong!'),
            '!help' => fn() => $message->reply($this->getHelpMessage()),
            '!store' => fn() => $message->reply('Our Official Webstore: ' . config('app.url')),
        ];

        $content = strtolower($message->content);
        if (isset($commands[$content])) {
            $commands[$content]();
        }
    }

    protected function getHelpMessage(): string
    {
        return "**Available Commands:**\n".
            "!ping - Check if bot is alive\n".
            "!store - Get store link\n";
    }

    protected function handleVerification($message)
    {
        $message->reply('Please visit our website to link your Discord account!');
    }

    protected function processRoleQueue(Discord $discord, $settings)
    {
        $pendingRoles = DiscordRoleQueue::where('processed', false)
            ->where('attempts', '<', 3)
            ->lockForUpdate()
            ->get();

        if ($pendingRoles->isEmpty()) {
            return;
        }

        foreach ($pendingRoles as $roleRequest) {
            try {
                if ($roleRequest->processed) {
                    continue;
                }

                $guild = $discord->guilds->get('id', $settings->discord_guild_id);
                if (!$guild) {
                    throw new \Exception("Guild {$settings->discord_guild_id} not found");
                }

                if (!$guild->members->cache->has($roleRequest->discord_id)) {
                    $guild->members->fetch($roleRequest->discord_id)->then(
                        function ($member) use ($guild, $roleRequest) {
                            if ($roleRequest->action === DiscordRoleQueue::GIVE_ROLE) {
                                $this->assignRole($guild, $member, $roleRequest);
                            } else {
                                $this->takeRole($guild, $member, $roleRequest);
                            }
                        },
                        function ($error) use ($roleRequest) {
                            $this->handleRoleError($roleRequest, "Member fetch failed: {$error->getMessage()}");
                        }
                    );
                } else {
                    $guild->members->fetch($roleRequest->discord_id)->then(
                        function ($member) use ($guild, $roleRequest) {
                            if ($roleRequest->action === DiscordRoleQueue::GIVE_ROLE) {
                                $this->assignRole($guild, $member, $roleRequest);
                            } else {
                                $this->takeRole($guild, $member, $roleRequest);
                            }
                        },
                        function ($error) use ($roleRequest) {
                            $this->handleRoleError($roleRequest, "Direct member fetch failed: {$error->getMessage()}");
                        }
                    );
                }

            } catch (\Exception $e) {
                $this->handleRoleError($roleRequest, $e->getMessage());
            }
        }
    }

    protected function assignRole($guild, $member, $roleRequest)
    {
        try {
            if (!$member) {
                throw new \Exception("Member {$roleRequest->discord_id} not found in guild");
            }

            $role = $guild->roles->get('id', $roleRequest->role_id);
            if (!$role) {
                throw new \Exception("Role {$roleRequest->role_id} not found in guild");
            }

            $member->addRole($role)->then(
                function () use ($roleRequest) {
                    $roleRequest->update([
                        'processed' => true,
                        'processed_at' => now(),
                        'error' => null
                    ]);

                    $this->info(sprintf(
                        '[%s] ✓ Role %s assigned to user %s',
                        now()->format('Y-m-d H:i:s'),
                        $roleRequest->role_id,
                        $roleRequest->discord_id
                    ));

                    Log::info('Discord role assigned', [
                        'discord_id' => $roleRequest->discord_id,
                        'role_id' => $roleRequest->role_id
                    ]);
                },
                function ($error) use ($roleRequest) {
                    $this->handleRoleError($roleRequest, "Role assignment failed: {$error->getMessage()}");
                }
            );
        } catch (\Exception $e) {
            $this->handleRoleError($roleRequest, $e->getMessage());
        }
    }

    protected function takeRole($guild, $member, $roleRequest)
    {
        try {
            if (!$member) {
                throw new \Exception("Member {$roleRequest->discord_id} not found in guild");
            }

            $role = $guild->roles->get('id', $roleRequest->role_id);
            if (!$role) {
                throw new \Exception("Role {$roleRequest->role_id} not found in guild");
            }

            $member->removeRole($role)->then(
                function () use ($roleRequest) {
                    $roleRequest->update([
                        'processed' => true,
                        'processed_at' => now(),
                        'error' => null
                    ]);

                    $this->info(sprintf(
                        '[%s] ✓ Role %s removed from user %s',
                        now()->format('Y-m-d H:i:s'),
                        $roleRequest->role_id,
                        $roleRequest->discord_id
                    ));

                    Log::info('Discord role removed', [
                        'discord_id' => $roleRequest->discord_id,
                        'role_id' => $roleRequest->role_id
                    ]);
                },
                function ($error) use ($roleRequest) {
                    $this->handleRoleError($roleRequest, "Role removal failed: {$error->getMessage()}");
                }
            );
        } catch (\Exception $e) {
            $this->handleRoleError($roleRequest, $e->getMessage());
        }
    }

    protected function syncRoles($settings)
    {
        $this->info('Starting role synchronization task...');

        // Retrieve the guild from the Discord API
        $guild = $this->discord->guilds->get('id', $settings->discord_guild_id);

        if (!$guild) {
            $this->error('Guild not found');
            return;
        }

        // Getting all roles from the guild
        $roles = $guild->roles;

        if (empty($roles)) {
            $this->info('No roles found in the guild.');
            return;
        }

        $currentRoleIDs = [];
        // Syncing roles with the database
        foreach ($roles as $role) {
            try {
                DiscordRole::updateOrCreate(
                    ['role_id' => $role->id],
                    ['name' => $role->name]
                );

                $currentRoleIDs[] = $role->id;

                $this->info(sprintf('Synced role: %s (%s)', $role->name, $role->id));
            } catch (\Exception $e) {
                $this->error(sprintf('Error syncing role %s: %s', $role->name, $e->getMessage()));
            }
        }

        // Getting all roles from the database
        $existingRoles = DiscordRole::pluck('role_id')
            ->where('deleted', 0)
            ->toArray();

        // Defining which roles need to be deleted
        $rolesToDelete = array_diff($existingRoles, $currentRoleIDs);

        if (!empty($rolesToDelete)) {
            DiscordRole::whereIn('role_id', $rolesToDelete)->update([
                'name' => DiscordRole::RAW('CONCAT(name, " [DELETED]")'),
                'deleted' => 1
            ]);
            $this->info(sprintf('Marked as removed %d roles from the database.', count($rolesToDelete)));
        } else {
            $this->info('No roles to remove.');
        }

        $this->info('Role synchronization task completed.');
    }


    protected function handleRoleError($roleRequest, $errorMessage)
    {
        $roleRequest->increment('attempts');
        $roleRequest->update([
            'error' => $errorMessage
        ]);

        $this->error(sprintf(
            '[%s] ✗ Failed to assign role %s to user %s: %s',
            now()->format('Y-m-d H:i:s'),
            $roleRequest->role_id,
            $roleRequest->discord_id,
            $errorMessage
        ));

        Log::error('Failed to assign Discord role', [
            'discord_id' => $roleRequest->discord_id,
            'role_id' => $roleRequest->role_id,
            'error' => $errorMessage
        ]);
    }
}
