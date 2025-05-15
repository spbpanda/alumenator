<?php

namespace App\Http\Controllers\Discord;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendMessageController extends Controller
{
    public static function sendErrorDiscordRcon($id, $setting): void
    {
        $data = [
            'title' => 'Oopppss 😔',
            'description' => 'Something went wrong with Order ID: #' . $id . '... RCON not able to connect to your server :( Please, make sure, that your server port is opened and you have enabled RCON in server.properties',
            'color' => 'EB1803',
            'fieldName' => "Make sure, that you're enabled RCON in server.properties",
            'fieldValue' => 'You need setup your RCON port, set enable-rcon=true',
            'username' => 'CONSOLE'
        ];

        self::sendDiscordMessage($data, $setting);
    }

    public static function sendErrorDiscordPlugin($id, $setting): void
    {
        $data = [
            'title' => 'Oopppss 😔',
            'description' => 'Something went wrong with Order ID: #' . $id . "... Our plugin not able to connect to your server :( Please, make sure, that you're using the latest plugin version and you setup your plugin correctly!",
            'color' => 'EB1803',
            'fieldName' => "Make sure, that you're using the latest plugin version",
            'fieldValue' => 'You can always get the LATEST plugin version in our Discord',
            'username' => 'CONSOLE'
        ];

        self::sendDiscordMessage($data, $setting);
    }

    public static function sendSuccessfulDiscord($username, $itemsCart, $setting): void
    {
        $items = [];
        foreach ($itemsCart as $iCart) {
            $product = Item::find($iCart->item_id);
            $items[] = $product->name . ' - ' . $product->price . ' ' . $setting->currency . ' (' . $iCart->count . ' qty)' . "\n";
        }

        $data = [
            'title' => __('Woooohooo! New donator 💪'),
            'description' => '**' . $username . '**' . ' ' . __('just bought something from our store! Thank him very much for his support!'),
            'color' => '0BD101',
            'fieldName' => __('Items:'),
            'fieldValue' => implode(', ', $items),
            'username' => $username
        ];

        self::sendDiscordMessage($data, $setting);
    }

    public static function sendDiscordMessage($data, $setting): void
    {
        $timestamp = date('c', strtotime('now'));
        $url = 'https://' . request()->getHost();
        $json_data = json_encode([
            'embeds' => [
                [
                    'title' => $data['title'],
                    'type' => 'rich',
                    'description' => $data['description'],
                    'url' => $url,
                    'timestamp' => $timestamp,
                    'color' => hexdec($data['color']),
                    'footer' => [
                        'text' => 'Powered by MINESTORECMS with ❤️', // You can't remove this unless you bought branding removal, otherwise your license will be suspended
                        'icon_url' => 'https://minestorecms.com/images/logo-levitation.png', // You can't remove this unless you bought branding removal, otherwise your license will be suspended
                    ],
                    "thumbnail" => [
                        "url" => "https://minotar.net/armor/bust/" . $data['username'] . "/190.png"
                    ],
                    'fields' => [
                        [
                            'name' => $data['fieldName'],
                            'value' => $data['fieldValue'],
                            'inline' => false,
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $ch = curl_init($setting->webhook_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $response = curl_exec($ch);
            curl_close($ch);
        } catch(\Exception $e) {
            Log::error('Discord Webhook Error: ' . $e->getMessage());
        }
    }
}
