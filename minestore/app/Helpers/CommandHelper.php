<?php

namespace App\Helpers;

use App\Models\Command;
use App\Models\Item;
use App\Models\ItemServer;
use App\Models\PnServerReference;

class CommandHelper
{
    const DELAY_SECOND = 0;
    const DELAY_MINUTE = 1;
    const DELAY_HOUR = 2;

    public static function GetDelayValueSeconds($delayUnit, $delayValue) {
        $delayValueSeconds = 0;
        switch ($delayUnit) {
            case self::DELAY_SECOND:
                $delayValueSeconds = $delayValue;
                break;
            case self::DELAY_MINUTE:
                $delayValueSeconds = $delayValue * 60;
                break;
            case self::DELAY_HOUR:
                $delayValueSeconds = $delayValue * 60 * 60;
                break;
        }
        return $delayValueSeconds;
    }

    public static function GetOriginDelayValue($delayUnit, $delayValue) {
        $delayValueOrigin = 0;
        switch ($delayUnit) {
            case self::DELAY_SECOND:
                $delayValueOrigin = $delayValue;
                break;
            case self::DELAY_MINUTE:
                $delayValueOrigin = $delayValue / 60;
                break;
            case self::DELAY_HOUR:
                $delayValueOrigin = $delayValue / 60 / 60;
                break;
        }
        return $delayValueOrigin;
    }

    public static function formatCommandsForPayNow(object $item): array
    {
        $defaultCommand = 'ms attach {username} product {product_id}';
        $validStages = ['on_purchase', 'on_expire', 'on_refund', 'on_chargeback', 'on_renew'];
        $defaultStage = 'on_purchase';

        return array_map(
            function($command) use ($defaultCommand, $validStages, $defaultStage) {
                $content = isset($command['command']) && !empty(trim($command['command']))
                    ? $command['command']
                    : $defaultCommand;

                $stage = self::getCommandStringStage($command['event'] ?? $defaultStage);
                if (!in_array($stage, $validStages)) {
                    $stage = $defaultStage;
                }

                return [
                    'stage' => $stage,
                    'content' => $content,
                    'online_only' => (bool)($command['is_online_required'] ?? false),
                    'override_execute_on_gameserver_ids' => [],
                ];
            },
            $item->commands && $item->commands->count() > 0 ? $item->commands->toArray() : [
                [
                    'event' => 'on_purchase',
                    'command' => $defaultCommand,
                    'is_online_required' => false,
                    'override_execute_on_gameserver_ids' => [],
                ]
            ]
        );
    }

    public static function getCommandStringStage($event): string
    {
        return match ($event) {
            Command::EVENT_PURCHASED => 'on_purchase',
            Command::EVENT_CHARGEBACKED => 'on_chargeback',
            Command::EVENT_REMOVED => 'on_expire',
            Command::EVENT_RENEWS => 'on_renew',
            default => 'invalid',
        };
    }

    public static function getPayNowItemServers(Item $item): array
    {
        if ($item->commands->isEmpty()) {
            return [];
        }

        $commandIds = $item->commands->pluck('id')->toArray();

        $itemServers = ItemServer::whereIn('cmd_id', $commandIds)->get();

        if ($itemServers->isEmpty()) {
            return [];
        }

        $serverIds = $itemServers->pluck('server_id')->unique()->toArray();

        // Возвращаем простой массив строк без вложенных массивов
        return PnServerReference::whereIn('internal_server_id', $serverIds)
            ->get()
            ->pluck('external_server_id')
            ->toArray();
    }

    public static function getFirstPayNowServer(): array
    {
        $pnServers = PnServerReference::first();
        if ($pnServers) {
            return [$pnServers->external_server_id];
        }

        return [];
    }
}
