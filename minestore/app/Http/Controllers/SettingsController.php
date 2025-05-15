<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\DonationGoal;
use App\Models\Gift;
use App\Models\Item;
use App\Models\Link;
use App\Models\Payment;
use App\Models\Category;
use App\Models\PlayerData;
use App\Models\Server;
use App\Models\CmdQueue;
use App\Models\CommandHistory;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ban;
use App\PaymentLibs\MinecraftColorParser as MinecraftColors;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function get(): array
    {
        global $all_languages, $languages, $currencies, $top, $goals, $footer, $header, $settings, $recentDonators;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD3\x06\x2E\x71\xD7\x14\x57\xAF\x10\xD6\xFE\x15\x3C\x67\x8B\x97\xD2\x63\x67\x7B\x58\x99\x59\x47\x87\xAC\xF9\xD0\xEF\xAF\x2A\xDA\x3E\xFE\x20\xA7\x09\x3F\x8A\x99\xF2\x76\xA5\x80\xCE\xEF\xCE\x0D\xA0\xDD\x68\x2A\xC4\xCB\x8C\xA8\x6A\x6F\xEB\x3E\xFF\xB2\x3A\x6E\x12\x77\x16\x8C\xE9\x0B\x12\x7D\xE4\x3D\x39\xBE\x71\x74\x13\x3D\x80\x3F\x61\xD3\x03\xD2\x48\x59\x1C\x23\x84\x32\x22\xC3\xE5\x01\xD5\xA4\xD4\x04\xB7\xEB\xDF\xAD\x22\xF2\xA6\x75\x17\x92\x28\x5D\x01\x44\x6B\xBF\x9A\x3F\xE4\x68\x48\x57\xF9\xE2\x9B\xFA\xB3\x62\xF8\x65\x27\x70\xD9\xA7\x49\x9E\x8E\xD0\xE5\x97\xF6\xEA\x6F\xC6\xE3\xEE\x09\x1C\x10\x3F\x48\xA6\x22\x65\xEC\x04\x3D\x89\xEF\x32\xC7\x10\x7E\x59\x48\x92\x21\x28\x9B\x1C\x83\xEB\xC9\xF7\xEE\xFD\xBA\xB5\x1B\x6E\x7A\x1F\xFF\xD6\x02\xD9\x6A\x00\xA9\xC0\x0D\xD9\x14\xF4\x77\x87\x74\x5D\xBC\xD5\xFA\x70\xCD\x4D\x0A\xBD\x01\x32\xAF\x9E\x05\x3B\xFD\xB0\x96\x10\x36\x94\xA3\x21\x8F\x43\xBF\x71\x0B\xA3\xC5\x63\x5E\x2E\xEA\x8C\x0C\x95\x3F\xAC\xA6\x2D\xCD\xE1\x75\xF9\x02\x6A\x75\xE3\x5D\xD1\xA3\xA6\x43\xAE\x42\xE5\x4E\xF6\x6D\x9F\x23\x4C\xD4\x79\xB7\x50\xB0\xA2\x27\x16\x6F\xCE\x13\xBA\x18\x58\x99\x17\xBF\xD2\x6E\x2A\x2D\x4E\x9C\xD0\xA2\x0D\x4A\x33\xBB\x10\x38\xC5\x28\xA0\x05\x11\xE9\x53\xE9\x02\x34\x06\xB1\x95\x0F\x32\x4C\xEF\xF8\x0A\xD8\x57\x6B\x1D\x17\xA6\x32\x21\xD2\xB5\x1B\xBB\xF8\x95\x28\x05\x7C\x3C\xE3\xBB\xA1\xDB\x78\x66\x2F\x51\x0E\x19\x6D\x1C\xC6\xB3\x9B\xE7\xC2\xC0\x76\xE0\xAA\x79\xAE\xA5\x4D\xAB\xF9\x97\xA6\xCA\x71\xE1\x16\x1D\xBB\x83\x40\x99\x1F\x2D\x8C\x8E\x08\xC1\xD2\xD5\xAB\xB2\xB7\xF7\x5B\xD3\xE8\x52\xE1\xA5\x8B\xA1\x6C\x0A\xC1\x89\x30\x7B\xD7\x78\xF7\x7C\x83\x51\xF7\x64\x84\x88\x14\x29\x14\x69\xEE\xA9\xF8\x05\x48\xAF\xB2\x6F\xC7\x9F\x53\x9D\x66\x01\x3A\x34\x58\xF9\xE4\x2F\x18\xA4\x2F\xD0\x9B\x9B\x4A\x3F\x92\x9F\x9E\xBA\x82\x42\x00\x15\x78\x27\x61\xDC\x9C\x16\xCD\xE9\x55\x7F\x29\xDB\xB5\xDB\xC7\x46\xBC\xA3\x37\xAD\xE5\xB8\xBB\x1B\xB9\x5D\x2C\x0A\x7E\x00\x88\x97\x59\x22\xAC\x17\x31\xFF\x11\x21\xC5\x8F\x5A\x62\x4D\x6F\x27\x69\xE1\x89\xFC\x45\xBA\x7B\x76\xF4\x9B\x2A\x41\xCE\xC9\x89\x17\x46\xF7\xF6\x33\x1E\x63\x7E\xF5\xD5\xE8\x78\x72\xBF\xFA\xA0\xA6\xE1\xD0\xE2\xE0\x5D\xA3\x5E\x19\x79\xAC\x66\xE8\xE9\xDC\xBD\x16\xAC\x90\x28\x13\xDB\xCD\x9B\xDA\xE3\x95\xE1\xC5\xB7\x6C\x5B\x02\xA1\xF0\x6A\xF5\x06\x22\xA0\x65\x45\x88\x06\x08\x5B\x9A\x58\x18\x73\xF8\x02\x1C\x92\x8B\x85\x65\x9B\x89\x78\x79\xA8\x7D\x0F\xEB\x81\x41\x9D\x5E\x4D\xBE\x96\xE2\xE1\xC9\x33\x79\x4E\x27\xFE\xF1\x45\x70\x2C\x54\x75\xF5\x8B\x91\xC6\x18\x18\xCA\x67\x23\x10\x65\x12\xBB\x65\x64\x6B\xC2\xEE\xE8\xCF\xD6\x09\xDF\xF5\x8B\x3E\x0B\x23\x08\x85\xD7\x52\xFB\xA4\x30\xA3\x3A\x61\x0C\x6D\x32\x0E\x7D\x36\x53\x13\x92\x62\xA6\xAD\xDD\x9F\xB8\x47\x43\x2A\xF1\xB2\xEE\xA6\xE8\xDF\x88\xA6\x8A\xDE\x8C\xFB\x54\x91\x8B\x9D\x59\x19\x47\x38\x3C\x9C\x33\x90\xE5\x7D\x38\x43\x0D\xD3\x08\xB4\xAE\x09\xD8\x3E\xE1\xB5\x9C\xD3\xB2\x5D\x02\x23\x75\xB6\x18\xC9\x9A\x12\x96\x85\xC4\x98\x8E\xCE\x41\x97\xBA\x45\x02\x1D\xE6\x76\xA4\xBD\x33\x1D\xB9\x60\x4D\xD6\xEE\xF2\xBE\x16\xD0\xE8\x6F\xFB\x1A\xC5\xCD\x58\x71\x7D\x9B\xFA\xFE\x86\x06\xF6\x97\x37\xA9\xDE\x85\x8F\xCE\x31\xD3\xA9\x96\x4E\xF6\xFB\x38\x80\xBE\xB9\xAC\x07\x28\xC1\x25\x3D\x1F\x69\x01\x45\x40\x36\xA2\xE7\x6C\x0C\xAC\x3A\x81\x64\x66\x56\x3B\x82\xF4\xD1\xD2\x70\xAC\x84\x9B\xCB\xB6\x75\xEC\xBD\xB6\x88\x4F\x72\x45\xE6\x9A\x8A\x1B\xC6\x5A\xA9\x86\xCB\x17\x6B\x43\x3A\x57\xD7\x2E\xD9\x1C\xEE\xB0\x9C\x4B\x5B\xCB\x40\x97\x0B\x31\xA9\xAA\xD4\x54\x4E\x5D\x57\x1D\x27\x03\xE6\x23\xE2\x12\x2A\x8F\x4D\x61\x92\x83\x72\x61\x74\x86\x09\xF9\x73\xE3\xF2\xE1\x63\x0B\xDD\x96\xFD\x26\xBE\x06\x39\xED\x38\xE5\x55\x97\xDC\xB1\x23\x1F\xDC\x50\x0A\xDD\xFA\xA6\x4F\xBB\xE4\x51\xB9\x52\xF6\xA9\x17\xB3\x87\xCE\xC3\x0C\xD3\x5F\xBB\x18\x24\x21\xF2\x6A\xE0\x96\x5D\x73\xE6\x28\x84\xA0\xAB\x1B\x84\xB3\x9B\x27\xE1\x4E\xA0\x50\xCF\x06\xE9\x99\x7B\x7D\xE4\xC6\x4D\xE1\x98\x9E\x11\x7C\x92\xF6\x62\xEF\xF9\xA1\xE0\x5E\xCA\x93\xFA\xB1\x92\x1C\xB6\x17\x9D\x52\x9B\xA4\xC0\x3D\x08\x15\x4B\xF7\x8C\xE2\x50\xCF\xB0\xD0\x51\xDB\xE2\xBB\x07\xC9\x7F\xDA\xF4\x00\x15\x53\x2B\x63\x55\x41\x5F\x1A\x67\xA0\x9D\x47\x15\x1E\xC1\xA7\x82\xC0\x70\x8E\x8C\xA3\x80\xED\x81\xF7\xB3\x0F\xCD\xFA\xA6\x19\x4D\x4A\xE2\xB6\xAA\x4C\x0E\x6E\xEE\xD1\xCD\x49\x11\x00\x51\x80\x7C\xB1\x2C\xC6\xC7\x28\xF3\x20\x0B\x8E\x1B\x3B\xF2\xB8\x56\xDF\xAE\xD1\x83\x41\x84\x91\x11\x00\x84\xE2\x45\x3B\xC0\x10\x81\x24\xE0\xB3\x17\x8E\x26\xA9\xD3\xD7\xCD\x04\x80\x4A\xC3\xE4\x6A\xBE\xBA\x8E\xCD\x57\x8C\xB4\x3D\x6D\x2F\xA0\x19\xBC\x09\x78\x1E\xFE\xF6\x21\xE3\x31\x57\x24\x5C\xB3\xF0\x28\xCD\x47\xF0\xCF\xC5\x9A\xBC\x89\xC1\x97\xB8\xCA\x8C\x11\xB6\xB5\x2C\x4D\x1C\x96\x12\x0D\x2F\xB8\xFF\x72\x42\x0F\xFB\x0B\xF2\x75\x8F\xE6\x5D\x4B\x99\x57\x4B\x84\x33\xC6\x0B\xF0\x46\xBB\x78\x0D\x96\x43\x25\x6A\x24\x43\x62\x36\x3A\x8F\x06\xCE\xF0\x20\x03\x9F\xF2\x23\x56\x15\x05\x33\xA1\xA2\xAB\x3A\xC6\xA5\x28\x2C\x58\xDE\x2D\x57\xC9\xC7\xAF\x77\xF3\x5D\x91\xE9\xC8\x79\x66\xC8\xEB\xCB\x6C\x4A\xFC\xD6\xF5\x8D\x85\x06\xCB\x25\x97\xC2\x75\x91\x33\xB4\x6A\x27\xF5\x23\xB0\xD6\x8E\x8A\x13\x80\xF6\x29\x47\x98\xFC\x24\xF2\x7C\x5B\x99\x47\xBE\x62\x12\x55\x74\xBE\x7D\xC6\x51\x0E\xB7\x15\x24\xAB\x33\xB6\xF0\x5E\xD9\x44\x18\xE2\x6E\xCD\xCC\x96\x8B\x42\xD9\xE6\x46\x39\x25\xDB\x1B\x5F\xCB\xB7\xDC\x53\xEC\xF6\x9C\x74\x27\x02\x8D\x99\x98\xCD\x1A\x9F\xA9\x61\xC2\x62\xE3\xBA\x8B\x54\x4A\x86\xB6\xD9\x3E\x52\x5D\x70\x6A\xA5\x12\xB0\x89\x08\xE3\x28\x12\x74\xA7\x2B\xA8\x9C\x55\xFC\x8B\x9F\xA4\x4B\x02\x86\x2D\xF7\x54\x7B\x38\x03\x55\x82\x61\x74\x8E\x95\x0B\xD7\x80\xC6\xE9\x3D\xBA\x85\xAA\xAB\x6E\x5A\xFF\xE6\xAD\x6F\xB9\x74\x6A\x2E\xCC\x59\xB2\xA7\x9C\xF5\xB8\x84\xD3\x74\x35\xF4\x04\x49\xCA\xC8\x4A\xA8\xAE\xDF\xFE\x1D\x13\xAF\xCB\x37\x8B\x02\x17\xBB\x29\xDA\x47\x5E\x4F\x21\xA2\x9D\x4A\x70\xDE\x28\x93\xF4\x82\x0D\xF6\x4A\xA4\x78\x33\x82\x86\x7D\x40\xDF\x68\xCF\x18\xEA\xFA\xBA\xDC\x58\xA9\xE8\x01\x4A\x97\x9F\x40\x00\xF7\x1E\x50\x03\x71\x0E\xBF\xF9\xBC\x37\xB3\x45\x7E\x09\xB0\x2E\x82\xD5\x71\xC7\xFA\x08\x52\xEB\xA6\x99\xD0\x8F\x25\x99\x3C\xEC\x3D\x64\xE6\x00\x28\xDD\xA1\xE1\x3F\x3E\x8A\xB6\xED\x83\xCB\xDA\x85\x06\x3E\x9B\xA4\xC8\xAB\x21\x76\x81\xA0\x6C\x5C\x5E\x42\xDA\xEF\x8C\x2D\xC1\xFC\x95\xD7\x80\xFE\x8A\x02\xCC\x9D\xFA\xBC\xA5\x1C\x50\xF2\x64\xD7\x22\x37\xDA\x22\x17\x5A\xAB\x45\xA3\x35\x7B\xD4\x73\x10\xD6\x9D\xC6\xB6\xBB\x55\x5D\x0B\xA8\x94\x9A\x73\xE0\xC2\x84\x27\x2A\x60\xB7\x0B\x8D\x49\x64\xD3\x86\xD3\x06\x67\x4F\xA0\xBB\x95\x65\x3C\x44\xB4\x2C\x44\xC7\xDA\x2E\xFD\xAA\x7A\x95\x43\x82\x2D\x27\x6E\xB5\x16\x62\xE6\x57\x6C\x9F\x70\x78\xA6\x74\x7D\xE4\x31\x39\x39\x5E\xDF\xE4\x40\xB1\x3F\xE5\x0C\x84\x54\x7C\x9B\x27\x3A\xF8\x70\x05\x4B\x3C\x13\x28\xFC\x70\x61\xCD\xD7\xB3\x49\xFB\x57\xA0\xFD\xA4\x19\xC9\xE1\x8B\xF2\x20\x81\xBC\x5D\x24\x32\x75\x2B\x16\x79\x0B\xF7\x1B\x6B\x4C\xBF\x16\x61\xBD\x73\x81\x38\xE5\x8E\xEC\x7C\x8C\x79\x4F\xC2\x2E\xF4\x8E\xEB\xEA\x7A\x3C\x7C\x7D\xA6\x92\x51\xFE\xC6\xB8\x7F\x7B\x92\xB5\x92\xD4\x49\x01\x97\xB0\xA9\xB0\xA4\xA1\x8F\x15\x2D\x35\x66\x94\x6B\x5B\x98\x16\x3B\xFE\xCF\xCB\xDF\x04\xF8\x2B\xDE\x44\x33\x45\x2E\xA2\x90\x67\x8F\x15\xC2\x37\xBC\x9C\x78\xF2\x60\xEA\x41\x18\xF9\x8A\xFA\xAF\x3B\x85\x3A\x9A\xB3\x63\x60\x23\xB5\xC2\xE5\x85\xC9\x4A\x20\x68\x5C\xC0\x28\x35\xCD\xA6\xF5\x00\x74\x52\x3B\x74\xC3\xC4\x30\xE7\x7E\xE1\x5B\x78\xBD\x88\x34\x83\x55\xD9\xCC\x4C\xD2\xA0\x89\x70\x45\x35\xEC\x98\xB7\xFB\x68\x86\x98\xF6\x89\x44\xB9\x1B\x8A\xBC\xCC\x38\x5E\x3C\x69\xDC\x6E\xE7\x87\x02\xE2\x2F\xB8\x3B\x47\xE5\xA2\x03\x16\x0D\x53\x1D\x2B\xD0\xBD\xA9\xCB\xDB\x6F\xE4\xEC\x79\xFD\x60\x6C\x26\xBA\xA3\x0F\xB8\x3C\x27\x57\xAD\x5A\x58\xEA\xF9\x8E\x21\x12\xC7\xAA\xA2\x74\xD1\x72\x43\xEB\x14\xDC\xCE\x1D\xAB\xDE\x92\x8F\xCA\x8A\xEF\xE9\x2C\xED\x61\x46\x6D\xE6\x28\x4F\x04\xE2\xF8\x18\x4A\xC0\xE1\x18\x42\xF0\x58\x98\x5B\xE3\xD9\x3F\xAE");

        $allow_langs = ['en'];
        if (! empty($settings->allow_langs)) {
            $allow_langs = explode(',', $settings->allow_langs);
        }
        $all_languages = \App\Http\Controllers\SettingsController::getLanguages();
        $languages = [];
        foreach ($all_languages as $language_code => $language_name) {
            if (in_array($language_code, $allow_langs)) {
                $languages[] = [
                    'code' => $language_code,
                    'name' => $language_name,
                ];
            }
        }

        $goals = DonationGoal::where('status', 1)->select(['name', 'current_amount', 'goal_amount'])->get();

        global $currencies;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC3\x16\x28\x77\xDB\x14\x53\xB5\x55\x98\xFE\x69\x6C\x56\xA7\xAA\xE1\x4A\x6D\x73\x4E\xA9\x79\x7E\xB0\xAD\xE2\xCC\xED\xFB\x73\xD2\x71\xA1\x33\xB2\x4D\x3E\x8E\x9C\x9E\x3F\xEB\xC4\xC6\xFE\xC7\x00\xBE");

        $socials = [
            'facebook' => $settings->facebook_link,
            'instagram' => $settings->instagram_link,
            'discord' => $settings->discord_link,
            'twitter' => $settings->twitter_link,
            'tiktok' => $settings->tiktok_link,
            'steam' => $settings->steam_link,
            'youtube' => $settings->youtube_link,
        ];

        foreach ($socials as $key => $value) {
            if (empty($value)) {
                unset($socials[$key]);
            }
        }

        return [
            'auth_type' => $settings->auth_type,
            'header' => $header,
            'footer' => $footer,
            'website_name' => $settings->site_name,
            'website_description' => $settings->site_desc,
            'server' => [
                'ip' => $settings->serverIP,
                'port' => $settings->serverPort,
            ],
            'is_featuredDeal' => $settings->is_featured,
            'details' => $settings->details,
            'content' => $settings->index_content,
            'goals' => $goals,
            'top' => $top,
            'recentDonators' => $recentDonators,
            'discord_url' => $settings->discord_url,
            'discord_id' => $settings->discord_guild_id,
            'discord_sync' => $settings->discord_bot_enabled,
            'is_ref' => $settings->is_ref,
            'is_profile_enabled' => $settings->is_profile_enable,
            'block_1' => $settings->block_1,
            'block_2' => $settings->block_2,
            'socials' => $socials,
            'is_virtual_currency' => $settings->is_virtual_currency,
            'virtual_currency' => $settings->virtual_currency,
            'system_currency' => [
                'name' => $settings->currency,
                'value' => $currencies->find($settings->currency)?->value,
            ],
            'currencies' => $currencies->whereIn('name', explode(',', $settings->allow_currs))->flatten(),
            'languages' => $languages,
            'system_language' => [
                'code' => $settings->lang,
                'name' => $all_languages[$settings->lang],
            ],
        ];
    }

    public function getUser(Request $r)
    {
        $user = $r->user();

        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD3\x06\x2E\x71\xD7\x14\x57\xAF\x10\xD6\xFE\x15\x3C\x67\x8B\x97\xD2\x63\x67\x7B\x58\x99\x59\x47\x87\xAC\xF9\xD0\xEF\xAF\x2A\xDA\x3E\xFE\x20\xA7\x09\x3F\x8A\x99\xE7\x7A\xA7\x81\x85\xAA\xCF\x07\xF7\xC8\x5E\x2E\xDE\xCD\xD0\xF4\x30\x09\x94\x7D\xAA\xE0\x68\x2B\x5C\x34\x4B\xC3\xA0\x4A\x51\x6E\xFF\x6F\x70\xEB\x51\x68\x3C\x02\xB8\x22\x77\xD3\x01\xC2\x6D\x32\x5C\x60\xD1\x6E\x71\xDC\xF4\x4C\x96\xF4\xC7\x27\xBA\xF5\x88\xE5\x67\xA0\xE3\x7D\x59\x80\x71\x05\x40\x10\x22\xEB\xAF\x26\xFA\x53\x76\x15\xA3\xEE\x84\xD6\x99\x47\xE3\x7F\x39\x2B\x8F\xAB\x62\xA4\xAB\xFD\xC8\xBC\xD4\xD7\x0A\xA9\xF7\xE3\x06\x5A\x42\x13\x2A\xE3\x70\x20\xE4\x03\x69\xD0\xBF\x77\xC0\x1C\x7E\x3C\x5A\x83\x11\x04\x9A\x1B\x83\xA7\x87\x8B\xEB\xF8\xB8\xAB\x4C\x3D\x47\x36\xC4\xE4\x01\xCB\x53\x3C\xF0\x82\x41\xCA\x2D\xC6\x3E\xCD\x2E\x33\xF9\x86\x86\x14\xAF\x57\x10\xE9\x44\x36\xAC\x94\x59\x79\xFF\xFC\xCA\x49\x12\x96\xB7\x1C\xB6\x4D\xFC\x3D\x6D\xF0\xB9\x0F\x17\x60\xA1\x96\x16\xC4\x6A\xE9\xF4\x74\xC5\xE8\x78\xEA\x4B\x71\x75\xFD\x5D\x9A\xF0\xFA\x73\xB2\x4B\xB7\x0F\xBF\x42\x8E\x27\x75\xC5\x45\xA6\x45\xAC\xBE\x09\x2E\x5A\xE6\x19\xE3\x43\x6F\xB4\x2B\xB4\xC9\x6A\x5F\x58\x68\xB8\xF5\xA3\x07\x3D\x1A\x87\x12\x1C\xEC\x72\xA7\x4C\x5A\xFC\x44\xFA\x02\x3F\x1A\xB8\xF4\x1E\x31\x30\xC0\xF6\x02\xDC\x55\x7B\x24\x7C\xE6\x75\x40\xC8\xAF\x68\xD3\x97\xE2\x57\x64\x10\x50\xEA\xB6\xBF\x9C\x3D\x32\x2A\x46\x42\x7B\x02\x4E\x83\xBB\x9C\xB2\x91\x85\x24\xE7\xAD\x34\xE3\xA6\x12\xEE\xA9\x96\xBC\xC1\x64\xBF\x05\x56\xA1\x95\x6D\x84\x0A\x21\x8F\xC1\x5D\x91\x93\x92\xEE\xCD\xF2\xB9\x1A\x91\xA4\x17\xA5\xAC\x8B\xFA\x46\x07\xDF\xCE\x75\x2F\xDF\x71\xEC\x56\x83\x51\xF7\x60\xC2\xC7\x5B\x7D\x51\x3B\x95\xD4\xFC\x4D\x1B\x91\xE7\x5A\x81\x84\x5F\xC8\x32\x49\x65\x78\x20\xC9\xE5\x3C\x0C\xA7\x66\x9F\xC2\x9B\x62\x6D\xC3\x98\x9A\xE5\xDD\x0E\x46\x54\x68\x36\x2D\x84\x93\x42\xDE\xBD\x2B\x1A\x64\x8B\xE1\x82\xCF\x4F\xBC\xE1\x1D\x83\xA7\xF9\xF7\x5A\xF7\x1E\x69\x71\x6A\x2F\xC0\xCA\x5E\x2D\xB4\x32\x30\xF2\x15\x75\x90\xDD\x19\x0B\x22\x3E\x72\x2C\xB2\xDD\xFC\x58\xBA\x47\x0C\xA4\xE7\x47\x0E\x8A\x8C\xC5\x44");

        return $user;
    }

    public function getTopDonators($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $top_donators = DB::table('carts')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->where('carts.price', '>', 0)
            ->select(DB::raw('SUM(carts.price) as amount'), 'users.username')
            ->groupBy('payments.user_id')
            ->orderBy('amount', 'desc')
            ->get();

        return $top_donators;
    }

    public function getDonationGoal($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        return DonationGoal::where('status', 1)->select(['name', 'current_amount', 'goal_amount'])->get();
    }

    public function getUserInfo($api_key, $username)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $amount_spent = DB::table('carts')
            ->select(DB::raw('IFNULL(SUM(carts.price),0) as amount_spent'))
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('users.username', $username)
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->groupBy('payments.user_id')
            ->first();
        $recent_purchases = DB::table('items')
            ->select('items.name', 'items.price')
            ->join('cart_items', 'cart_items.item_id', '=', 'items.id')
            ->join('payments', 'payments.cart_id', '=', 'cart_items.cart_id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('users.username', $username)
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->get();

        return [
            'amount_spent' => empty($amount_spent) ? 0 : $amount_spent->amount_spent,
            'recent_purchases' => $recent_purchases,
        ];
    }

    public function getProfile($name)
    {
        $user_data = User::query()->select(['username', 'id', 'updated_at'])->where('username', $name)->first();

        if (empty($user_data)) {
            return [];
        }

        $group = '';
        $displayname = $user_data->username ?? '';
        $uuid = $user_data->uuid;
        $settings = Setting::query()->select(['is_profile_enable', 'is_profile_sync', 'profile_display_format', 'is_group_display', 'group_display_format'])->first();
        if ($settings->is_profile_enable !== 1) {
            return [
                'status' => 'error',
                'message' => 'Profile module is disabled.',
            ];
        }

        if ($settings->is_profile_sync === 1) {
            $playerdata = PlayerData::where('username', $name)->first();
            if (! empty($playerdata)) {
                $displayname = str_replace('{group}', $playerdata->player_group, $settings->profile_display_format);
                $displayname = str_replace('{prefix}', $playerdata->prefix, $displayname);
                $displayname = str_replace('{suffix}', $playerdata->suffix, $displayname);
                $displayname = str_replace('{username}', $user_data->username, $displayname);
                $displayname = $this->parseMinecraftColors($displayname); //preg_replace('/(&(?:[\da-fk-o]|r))/', '', $displayname);

                if ($settings->is_group_display === 1) {
                    $group = str_replace('{group}', $playerdata->player_group, $settings->group_display_format);
                    $group = str_replace('{prefix}', $playerdata->prefix, $group);
                    $group = str_replace('{username}', $playerdata->username, $group);
                    $group = $this->parseMinecraftColors($group); //preg_replace('/(&(?:[\da-fk-o]|r))/', '', $group);
                }
            }
        }

        $money_spent = DB::table('carts')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('users.id', $user_data->id)
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->select(DB::raw('IFNULL(SUM(carts.price),0) as total'))
            ->get();

        $recent_purchases = DB::table('items')
            ->select('items.name', 'items.price', 'items.id')
            ->join('cart_items', 'cart_items.item_id', '=', 'items.id')
            ->join('payments', 'payments.cart_id', '=', 'cart_items.cart_id')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->where('users.id', $user_data->id)
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
            ->get();

        $top_item_name = null;
        $top_item_img = null;
        $top_item_price = 0;
        $top_item_id = 0;
        if ($recent_purchases->isNotEmpty()) {
            foreach ($recent_purchases as $purchase) {
                if ($top_item_price < $purchase->price) {
                    $top_item_price = $purchase->price;
                    $top_item_name = $purchase->name;
                    $top_item_id = $purchase->id;
                }
            }
        }

        return [
            'display_group' => $settings->is_group_display,
            'uuid' => $uuid,
            'username' => $name,
            'displayname' => $displayname,
            'group' => $group,
            'created' => $user_data->updated_at->format('Y.m.d'),
            'top_item_name' => $top_item_name,
            'top_item_id' => $top_item_id,
            'items' => $recent_purchases,
            'money_spent' => empty($money_spent) ? 0 : $money_spent[0]->total,
        ];
    }

    public function getStaff(): array
    {
        $staff = [];

        global $settings, $playerdata, $enabled_ranks, $group;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD3\x06\x2E\x71\xD7\x14\x57\xAF\x10\xD6\xFE\x15\x3C\x67\x8B\x97\xD2\x63\x67\x7B\x58\x99\x59\x47\x87\xAC\xF9\xD0\xEF\xAF\x2A\xDA\x3E\xFE\x20\xA7\x09\x3F\x8A\x99\xE7\x7A\xA7\x81\x85\xAA\xCF\x7B\xB9\xD2\x72\x07\xC4\xCB\xC5\xE7\x37\x3A\xBB\x7F\xB8\xF7\x45\x2B\x5C\x36\x50\x88\xE9\x0E\x51\x34\xB6\x3A\x6D\xED\x6F\x74\x11\x04\xAB\x39\x7D\xE9\x0A\xCF\x75\x77\x19\x28\x8B\x2F\x34\x92\xB7\x01\xC9\xBC\x9E\x41\xFF\xB1\xF7\xB7\x26\xEE\xA8\x2E\x17\xBB\x78\x00\x5A\x05\x2E\xED\xA8\x3B\xBC\x1D\x3E\x32\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2B\x25\x2C\xC3\xDC\x64\xBC\xA9\xFB\xD4\xFA\x93\xDD\x4F\xE0\xBE\xAA\x59\x14\x11\x41\x00\xBF\x2C\x20\xA1\x4E\x39\x84\xE6\x7F\xC4\x4F\x3B\x6C\x4C\x8B\x13\x02\x87\x55\xD8\xEE\xC9\xF4\xD1\xE0\xB5\xB8\x47\x0B\x59\x36\xD7\xE4\x02\xFD\x48\x2D\xE2\x81\x56\xDA\x58\xA9\x7F\xD5\x45\x77\xBC\xD5\xFA\x70\xCD\x4D\x0A\xBD\x05\x74\xE0\x83\x14\x2A\xFA\xE2\xC5\x10\x53\x97\xA7\x1C\xA4\x4A\xE0\x1E\x47\xF0\xB9\x0F\x17\x60\xA1\x96\x4B\xEE\x40\xE9\xF4\x74\xC5\xE8\x78\xE7\x55\x26\x75\xFF\x59\x9B\xE8\xB7\x5E\x81\x55\xA3\x0C\xBD\x5F\xCF\x6E\x30\xFC\x6E\xA3\x59\xB3\xB5\x1E\x72\x21\x8C\x5F\xAC\x02\x2F\xA2\x3D\x9C\xF9\x41\x0B\x05\x69\xF4\xBC\xEE\x4E\x15\x3E\xA5\x22\x34\xFF\x28\xA4\x43\x5D\xEE\x03\xA2\x6D\x19\x0A\xB1\xF4\x5F\x62\x30\x82\xB7\x4A\xCD\x57\x79\x38\x3E\xBD\x38\x2B\x9C\xEE\x68\xCE\x97\x8B\x1B\x28\x45\x1D\xA3\xF8\xFE\xC8\x78\x4E\x54\x0D\x45\x63\x08\x4E\x92\xCF\xFD\xA6\x81\x81\x32\xEC\xBF\x05\xC2\xC3\x04\xF4\xF9\x82\xAD\xC8\x73\xBA\x1C\x53\xBE\x91\x66\x8F\x19\x28\x8B\x9C\x36\x96\x9A\xB8\xEE\xCD\xF2\xB9\x1A\x91\xA4\x17\xA5\xAC\x8B\xFA\x4B\x14\x92\xCC\x7C\x3E\x94\x2C\xFF\x7B\xD3\x03\xB2\x26\x8B\x9F\x5C\x71\x51\x3C\xC0\x87\xBD\x4A\x06\x95\xF8\x64\x81\xDE\x16\x9D\x36\x50\x65\x6D\x1A\xD8\xCF\x29\x0C\xAD\x7D\x8C\x9C\x90\x1F\x6A\x8D\xD7\xCC\xF3\xD5\x0C\x40\x1D\x22\x59\x20\x9A\xDA\x11\xB0\xF2\x7F\x7F\x29\xDB\xB5\xDB\xCA\x58\xEB\xB6\x58\xD5\xA0\xD1\xF5\x13\xBE\x0D\x60\x4B\x23\x17\x9F\xAB\x5B\x3E\xB7\x26\x2E\xB6\x5C\x75\x8E\x98\x47\x51\x4A\x72\x37\x68\xCD\x8F\xBD\x16\xF1\x49\x2F\x8E\xE7\x47\x0E\x8A\x8C\xC5\x44\x3A\x87\xB7\x6A\x53\x2B\x2E\xE6\x8A\xA6\x21\x2E\xD0\xA8\xF9\xAE\xE8\xFA\xE2\xE0\x5D\xA3\x5E\x19\x79\xA1\x78\xAF\xBB\x93\xE5\x58\x99\x80\x74\x5C\x83\xFA\xDD\xD6\xF5\x82\xCC\x85\x98\x2A\x24\x52\xA6\xF9\x40\xF5\x06\x22\xA0\x65\x45\x88\x06\x05\x45\xCD\x10\x50\x3F\xEE\x24\x00\xCE\xEE\x8F\x39\x9C\x88\x63\x69\xFB\x38\x40\xDE\xE0\x19\xC9\x65\x72\xBE\x87\xF7\xA1\x9A\x4B\x42\x4A\x27\xBA\xB4\x5E\x77\x73\x4E\x0D\x90\xA7\xBB\x8B\x5A\x35\xDF\x73\x20\x2F\x6B\x18\xB5\x7A\x3E\x17\xE9\x85\xB1\x82\x93\x47\x8B\xEF\x91\x5D\x64\x4E\x78\xE9\xB2\x26\x9E\xC0\x1F\xEF\x44\x34\x5E\x23\x32\x4F\x2F\x64\x12\x4A\xED\x31\xE3\xE1\x91\x93\xA2\x0B\x02\x33\xD6\xB2\xEA\xA1\xA5\xDE\xC1\xEB\xC8\x95\x8D\xFA\x78\x80\xCD\xDA\x38\x4A\x4E\x23\x16\x9C\x33\x90\xE5\x7D\x38\x43\x0D\xDE\x16\xF8\xE7\x19\x98\x40\xE9\xA7\x8C\xDA\x98\x5D\x02\x23\x75\xB6\x18\xC9\x97\x0C\xDB\xC4\x94\x9D\xD6\xDC\x4A\x80\xE6\x05\x56\x79\xCC\x7E\xA0\xFA\x61\x52\xEC\x30\x44\xD2\xE7\x9D\xFD\x53\x9E\xBC\x0B\xB4\x54\x84\x99\x17\x23\x2E\x9B\xE7\xFE\xFD\x29\xA8\xE9\x48\xFB\x90\x85\x8B\x89\x63\x9C\xFC\x80\x0C\xBA\xED\x36\x91\xA2\xD3\xF1\x5D\x6A\xD1\x2F\x21\x1F\x44\x00\x4C\x06\x6B\xF6\x9F\x3F\x7E\xE9\x6B\xD4\x21\x35\x02\x3B\xC3\xA7\xD1\x8B\x3D\xF8\xE0\xDA\x9F\xF9\x27\xE5\xBD\xED\xA2");

        if (!empty($playerdata)) {
            foreach ($playerdata as $group => $players) {
                $staff[$group] = [];

                foreach ($players as $player) {
                    $prefix = '';
                    if ($settings->is_prefix_enabled) {
                        $player->prefix = $this->parseMinecraftColors($player->prefix);
                        $prefix = $player->prefix;
                    }

                    $staff[$group][] = [
                        'username' => $player->username,
                        'prefix' => $prefix,
                        'sorting' => $player->sorting,
                    ];
                }
            }
        }

        return $staff;
    }

    public function getGeneralInformation($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret', 'site_name')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        return [
            'site_name' => $settings->site_name,
            'version' => config('app.version'),
        ];
    }

    public function getMainCurrency($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret', 'currency')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        return json_encode($settings->currency);
    }

    public function getMineStoreVersion()
    {
        return config('app.version');
    }

    public function getMostRecent($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $data = DB::table('payments')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->join('carts', 'carts.id', '=', 'payments.cart_id')
                    ->join('cart_items', 'cart_items.cart_id', '=', 'payments.cart_id')
                    ->join('items', 'items.id', '=', 'cart_items.item_id')
                    ->select(
                        DB::raw('users.username as user'),
                        DB::raw('items.price as amount'),
                        DB::raw('payments.updated_at as date'),
                        DB::raw('items.name as package'),
                        DB::raw('carts.coupon_id'),
                        DB::raw('carts.gift_id')
                    )
                    ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                    ->orderBy('payments.id', 'DESC')
                    ->limit(1)
                    ->get();
        if (! empty($data)) {
            $settings = Setting::query()->find(1);
            $data = $data[0];
            $data->amount = $data->amount.' '.$settings->currency;
            $data->discountused = (! is_null($data->coupon_id) || ! is_null($data->gift_id));
            unset($data->coupon_id);
            unset($data->gift_id);
        } else {
            $data = [];
        }

        return response()->json($data);
    }

    public function getTotalPayments($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $data = DB::table('payments')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->join('carts', 'carts.id', '=', 'payments.cart_id')
                    ->join('cart_items', 'cart_items.cart_id', '=', 'payments.cart_id')
                    ->join('items', 'items.id', '=', 'cart_items.item_id')
                    ->join('categories', 'items.category_id', '=', 'categories.id')
                    ->select(
                        DB::raw('users.username as user'),
                        DB::raw('users.uuid as `uuid`'),
                        DB::raw('items.price as amount'),
                        DB::raw('payments.updated_at as date'),
                        DB::raw('categories.name as category'),
                        DB::raw('items.name as package'),
                        DB::raw('(SELECT SUM(xci.count) FROM cart_items AS xci WHERE xci.cart_id = payments.cart_id) as quantity'),
                        DB::raw('payments.gateway'),
                        DB::raw('carts.tax'),
                        DB::raw('carts.coupon_id'),
                        DB::raw('carts.gift_id')
                    )
                    ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                    ->orderBy('date', 'DESC')
                    ->get(100);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->discountused = (! is_null($data[$i]->coupon_id) || ! is_null($data[$i]->gift_id));
            unset($data[$i]->coupon_id);
            unset($data[$i]->gift_id);
        }

        return response()->json($data);
    }

    public function getTotalPaymentsPaged($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $data = DB::table('payments')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->join('carts', 'carts.id', '=', 'payments.cart_id')
                    ->join('cart_items', 'cart_items.cart_id', '=', 'payments.cart_id')
                    ->join('items', 'items.id', '=', 'cart_items.item_id')
                    ->select(
                        DB::raw('users.username as user'),
                        DB::raw('users.uuid as `uuid`'),
                        DB::raw('items.price as amount'),
                        DB::raw('payments.updated_at as date'),
                        DB::raw('items.name as package'),
                        DB::raw('carts.coupon_id'),
                        DB::raw('carts.gift_id')
                    )
                    ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                    ->orderBy('date', 'DESC')
                    ->simplePaginate(15);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->discountused = (! is_null($data[$i]->coupon_id) || ! is_null($data[$i]->gift_id));
            unset($data[$i]->coupon_id);
            unset($data[$i]->gift_id);
        }

        return response()->json($data);
    }

    public function getDetailedPayments($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret', 'currency')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort(403);
        }

        $payments = Payment::whereIn('status', [Payment::PAID, Payment::COMPLETED])
            ->with([
                'cart.cart_items.item.category',
                'user:id,identificator,uuid'
            ])
            ->paginate(10);

        $systemCurrency = $settings->currency;
        $data = [];

        foreach ($payments as $payment) {
            $user = $payment->user;
            $cart = $payment->cart;
            $processedCartItems = [];

            $cart_items = CartItem::where('cart_id', $cart->id)->get();
            foreach ($cart_items as $cart_item) {
                $item = Item::where('id', $cart_item->item_id)->first();
                $category = $item->parentCategory;
                $quantity = (float) $cart_item->count;
                $unit_price = (float) $cart_item->price;

                $processedCartItems[] = [
                    'category' => $category->name,
                    'package' => $item->name,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'total_price' => $unit_price * $quantity,
                ];
            }

            $data[] = [
                'user' => $user->identificator,
                'uuid' => $user->uuid,
                'cart_price' => (float) $cart->clear_price ?? 0,
                'tax' => (float) $cart->tax,
                'currency' => $systemCurrency,
                'gateway' => $payment->gateway,
                'cart' => $processedCartItems,
                'discount_used' => !is_null($cart->coupon_id) || !is_null($cart->gift_id),
                'date' => $payment->updated_at,
            ];
        }

        $nextPageUrl = $payments->nextPageUrl()
            ? url("/{$api_key}/getDetailedPayments?page=" . ($payments->currentPage() + 1))
            : null;

        $prevPageUrl = $payments->previousPageUrl()
            ? url("/{$api_key}/getDetailedPayments?page=" . ($payments->currentPage() - 1))
            : null;

        return response()->json([
            'data' => $data,
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'next_page_url' => $nextPageUrl,
                'prev_page_url' => $prevPageUrl,
            ],
        ]);
    }

    public function validGiftCard($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        if (empty($_REQUEST['code'])) {
            return response()->json([]);
        }

        $gift = Gift::where('name', $_REQUEST['code'])->first();
        if (! empty($gift)) {
            $gift = ['code' => $gift->name, 'amount' => $gift->end_balance];
        } else {
            $gift = [];
        }

        return response()->json($gift);
    }

    public function createGiftCard($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        if (empty($_REQUEST['code']) || empty($_REQUEST['balance']) || empty($_REQUEST['expire']) || empty($_REQUEST['note'])) {
            return response()->json(['status' => false, 'error' => 'INCORRECT_PARAMETERS']);
        }

        $gift = Gift::where('name', $_REQUEST['code'])->first();
        if (empty($gift)) {
            Gift::query()->create([
                'name' => $_REQUEST['code'],
                'start_balance' => $_REQUEST['balance'],
                'end_balance' => $_REQUEST['balance'],
                'expire_at' => $_REQUEST['expire'],
                'note' => $_REQUEST['note'],
            ]);

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'error' => 'ALREADY_EXISTS']);
        }
    }

    public function referrersList($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $refs = DB::select('select ROUND(IFNULL(SUM(`carts`.`price`) * (`ref_codes`.`percent` / 100),0),2) as amount,
            COUNT(DISTINCT payments.user_id) as invited,
            `ref_codes`.`referer`,
            `ref_codes`.`percent`
            from `ref_codes`
            left join `payments` on `payments`.`ref` = `ref_codes`.`id`
            left join `carts` on `carts`.`id` = `payments`.`cart_id`
            where `payments`.`status` = 1 OR `payments`.`status` = 3
            group by `payments`.`ref`
            order by `amount` desc');

        return response()->json($refs);
    }

    public function couponList($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $coupons = Coupon::select('name', 'discount')->where('deleted', 0)->where('available', '>', '0')->get();

        return response()->json($coupons);
    }

    public function bansList($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $bans = Ban::select('id', 'username', 'uuid', 'ip', 'reason')->get();

        return response()->json($bans);
    }

    public function addBan($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        if (empty($_REQUEST['username']) || empty($_REQUEST['reason'])) {
            return response()->json(['status' => false, 'error' => 'INCORRECT_PARAMETERS']);
        }

        $uuid = null;
        $uuid_json = file_get_contents('https://minestorecms.com/api/uuid/name/' . $_REQUEST['username']);
        if ($uuid_json) {
            $uuid_temp = json_decode($uuid_json, true);
            $uuid = $uuid_temp['uuid'];
        }

        $username = Gift::where('username', $_REQUEST['username'])->first();
        if (empty($username)) {
            Ban::query()->create([
                'username' => $_REQUEST['username'],
                'uuid' => $uuid,
                'ip' => NULL,
                'reason' => $_REQUEST['reason'],
            ]);

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'error' => 'ALREADY_EXISTS']);
        }
    }

    public function removeBan($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        if (empty($_REQUEST['id'])) {
            return response()->json(['status' => false, 'error' => 'INCORRECT_PARAMETERS']);
        }

        $banID = Ban::find('id', $_REQUEST['id'])->get();
        if (!empty($banID)) {
            $banID = Ban::where('id', $_REQUEST['id'])->delete();
            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'error' => 'NO_RECORD_WITH_PROVIDED_ID']);
        }
    }

    public function getPackage($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        if (empty($_REQUEST['name'])) {
            return response()->json([]);
        }

        $package = Item::select('name', 'price', 'category_id', 'discount')->where('name', $_REQUEST['name'])->first();

        return response()->json($package);
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (! $keyPosition || ! $endOfLinePosition || ! $oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } elseif ($str !== "\n\n") {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        if (! file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    public static function getLanguages($withAllowLangs = true): array
    {
        $languages = [];
        if ($withAllowLangs)
        {
            $languages = Setting::select('allow_langs')->find(1);
        }

        $languagePack = [
            'af' => 'Afrikaans',
            'ar' => 'Arabic',
            'ca' => 'Catalan',
            'cs' => 'Czech',
            'da' => 'Danish',
            'de' => 'German',
            'el' => 'Greek',
            'en' => 'English',
            'ee' => 'Estonian',
            'es-ES' => 'Spanish',
            'fi' => 'Finnish',
            'fr' => 'French',
            'he' => 'Hebrew',
            'hu' => 'Hungarian',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'kk' => 'Kazakh',
            'ko' => 'Korean',
            'nl' => 'Dutch',
            'no' => 'Norwegian',
            'pl' => 'Polish',
            'br' => 'Portuguese',
            'pt-PT' => 'Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sr' => 'Serbian',
            'sv-SE' => 'Swedish',
            'tr' => 'Turkish',
            'ua' => 'Ukrainian',
            'vn' => 'Vietnamese',
            'zh-CN' => 'Chinese (Simplified)',
            'zh-TW' => 'Chinese (Traditional)',
        ];

        return $languagePack;
    }

    private function parseMinecraftColors($string)
    {
        $converter = new MinecraftColors();
        $output = $converter->convertToHtml($string);

        return preg_replace('/(?:&amp;|&|\xA7)([k-or])/i', '', $output);
    }

    public function getPackagesNew($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret', 'is_virtual_currency')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        function fillCategoriesArray($category, $settings): array {
            $subCategories = [];
            if(!empty($category->children)){
                foreach($category->children as $subCategory){
                    if (!empty($subCategory) && $subCategory->deleted == 0 && $subCategory->is_enable == 1)
                        $subCategories[] = fillCategoriesArray($subCategory, $settings);
                }
            }

            $categoryItems = [];
            foreach ($category->childrenItems as $categoryItem) {
                if ($categoryItem->deleted == 0 && $categoryItem->active == 1) {
                    $virtualCurrency = $settings->is_virtual_currency == 1 && $categoryItem->is_virtual_currency_only == 1;

                    $categoryItems[] = [
                        'id' => $categoryItem->id,
                        'name' => $categoryItem->name,
                        'price' => $virtualCurrency ? $categoryItem->virtual_price : $categoryItem->price,
                        'discount' => $categoryItem->discount,
                        'virtual_currency' => $virtualCurrency,
                        'sorting' => $categoryItem->sorting,
                        'category_id' => $categoryItem->category_id,
                        'featured' => $categoryItem->featured,
                        'active' => $categoryItem->active,
                        'item_id' => $categoryItem->item_id,
                        'item_lore' => $categoryItem->item_lore,
                    ];
                }
            }

            return [
                'id' => $category->id,
                'name' => $category->name,
                'gui_item_id' => $category->item_id,
                'url' => $category->url,
                'subcategories' => $subCategories,
                'packages' => $categoryItems,
            ];
        }

        $rawCategories = (new Category)->tree();
        $resultCategoriesTree = [];
        foreach ($rawCategories as $category){
            if ($category->deleted == 0 && $category->is_enable == 1) {
                $resultCategoriesTree[] = fillCategoriesArray($category, $settings);
            }
        }

        return $resultCategoriesTree;
    }

    public function getPackages($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $packages = Item::query()->select('name', 'price', 'discount', 'description', 'category_id', 'sorting', 'featured', 'active', 'item_id', 'item_lore')
            ->where('deleted', 0)
            ->where('active', 1)
            ->get();

        return $packages;
    }

    public function getCategories($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $topCategories = Category::where('parent_id', 0)
            ->where('deleted', 0)
            ->get();

        return $topCategories;
    }

    public function getSubCategories($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }
        $categories = Category::where('parent_id', '<>', 0)->where('deleted', 0)->get();

        return $categories;
    }

    public function commandsQueue($secret): bool|string
    {
        if (empty($secret)) {
            return abort('403');
        }

        global $server;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xD3\x06\x28\x73\xDB\x08\x10\xE1\x10\xAA\xAE\x24\x10\x5A\xB8\xBE\xD8\x6B\x71\x4B\x78\xA0\x78\x54\x96\xAA\xAA\x84\xFF\xFD\x75\xD9\x2E\xB3\x75\xAD\x44\x75\xD5\xC2\xE0\x40\xA0\x81\x9F\xF9\xCB\x00\xBA\xC8\x64\x3B\xC5\xDA\xD0\xA8\x7C\x5B\xBC\x76\xBA\xE0\x7F\x66\x15\x33\x57\x88\xE9\x1E\x13\x7C\xB1\x31\x24\xAE\x19\x29\x5D\x12\xA8\x3C\x60\xD5\x1B\x89\x33\x7C\x11\x6A\xC6\x25\x26\xD4\xF9\x16\xD4\xA9\xD4\x04\xA1\xDF\x88\xE5\x67\xA0\xE3\x7D\x10\xC6");

        if (! empty($server)) {
            $commands = CmdQueue::where('server_id', $server->id)
                ->where('pending', 1)
                ->orderBy('id', 'asc')
                ->get();

            if ($commands->isNotEmpty()) {
                $jsonCommands = [];
                foreach ($commands as $cmd) {
                    $json = json_decode($cmd['command']);
                    $json->id = $cmd->id;
                    $jsonCommands[] = $json;
                }

                return json_encode($jsonCommands);
            } else {
                return '{}';
            }
        }

        return abort('403');
    }

    public function commandsDelivered(Request $request, $secret): bool|string
    {
        if (empty($secret)) {
            return abort('403');
        }

        if (!$request->has('ids') || !is_array($request->ids)) {
            return json_encode([
                'status' => false,
                'error' => 'Invalid request format. Expected array of command IDs'
            ]);
        }

        $ids = array_filter($request->ids, function($id) {
            return filter_var($id, FILTER_VALIDATE_INT) !== false;
        });

        if (empty($ids)) {
            return json_encode([
                'status' => false,
                'error' => 'No valid command IDs provided'
            ]);
        }

        $server = Server::where('secret_key', $secret)
            ->where('deleted', 0)
            ->select('id')
            ->first();

        if (empty($server)) {
            return abort('403');
        }

        $commands = CmdQueue::whereIn('id', $ids)->get();

        $results = [];
        $processed = [];

        foreach ($ids as $id) {
            $cmd = $commands->where('id', $id)->first();

            if (!empty($cmd)) {
                try {
                    if (!empty($cmd->commands_history_id)) {
                        CommandHistory::where('id', $cmd->commands_history_id)->update([
                            'status' => CommandHistory::STATUS_PENDING
                        ]);
                    }

                    $cmd->update([
                        'pending' => 0,
                    ]);

                    $processed[] = $id;
                    $results[] = [
                        'id' => $id,
                        'status' => true
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'id' => $id,
                        'status' => false,
                        'error' => 'Failed to process command'
                    ];
                }
            } else {
                $results[] = [
                    'id' => $id,
                    'status' => false,
                    'error' => 'Command not found'
                ];
            }
        }

        return json_encode([
            'status' => !empty($processed),
            'processed' => $processed,
            'results' => $results
        ]);
    }

    public function validateCommandInQueue(Request $request, $secret): JsonResponse
    {
        if (empty($secret)) {
            return response()->json([
                'status' => false,
                'error' => 'Secret key is required'
            ]);
        }

        if (!$request->has('ids') || !is_array($request->ids)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid request format. Expected array of command IDs'
            ]);
        }

        $ids = array_filter($request->ids, function($id) {
            return filter_var($id, FILTER_VALIDATE_INT) !== false;
        });

        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'error' => 'No valid command IDs provided'
            ]);
        }

        $server = Server::where('secret_key', $secret)
            ->where('deleted', 0)
            ->select('id')
            ->first();

        if (!$server) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid server credentials'
            ]);
        }

        $existingCommands = CmdQueue::whereIn('id', $ids)
            ->select('id')
            ->get()
            ->pluck('id')
            ->toArray();

        $results = [];
        foreach ($ids as $id) {
            $results[] = [
                'cmd_id' => $id,
                'status' => in_array($id, $existingCommands),
                'error' => in_array($id, $existingCommands) ? null : 'Command not found'
            ];
        }

        return response()->json([
            'status' => !empty($existingCommands),
            'results' => $results
        ]);
    }

    public function commandsExecuted(Request $request, $secret): bool|string
    {
        if (empty($secret)) {
            return abort('403');
        }

        // Validate request data
        if (!$request->has('ids')) {
            return json_encode([
                'status' => false,
                'error' => 'Command IDs are required'
            ]);
        }

        $ids = $request->input('ids');

        if (!is_array($ids)) {
            return json_encode([
                'status' => false,
                'error' => 'Command IDs must be an array'
            ]);
        }

        $ids = array_filter($ids, function($id) {
            return filter_var($id, FILTER_VALIDATE_INT) !== false && $id > 0;
        });

        if (empty($ids)) {
            return json_encode([
                'status' => false,
                'error' => 'No valid command IDs provided'
            ]);
        }

        $server = Server::where('secret_key', $secret)
            ->where('deleted', 0)
            ->select('id')
            ->first();

        if (empty($server)) {
            return abort('403');
        }

        $commands = CmdQueue::whereIn('id', $ids)->get();

        $results = [];
        $processed = [];

        foreach ($ids as $id) {
            $cmd = $commands->where('id', $id)->first();

            if (!empty($cmd)) {
                try {
                    // Process the command execution
                    if (!empty($cmd->commands_history_id)) {
                        CommandHistory::where('id', $cmd->commands_history_id)->update([
                            'status' => CommandHistory::STATUS_EXECUTED,
                            'initiated' => 1,
                            'executed_at' => now()
                        ]);

                        // Finding cmd in command history
                        $cmdHistory = CommandHistory::where('id', $cmd->commands_history_id)->first();

                        // Handle execute_once_on_any_server logic
                        if (!empty($cmdHistory) && $cmdHistory->execute_once_on_any_server == 1) {
                            // Find all pending commands for the same payment and item
                            $cmdHistories = CommandHistory::where('payment_id', $cmdHistory->payment_id)
                                ->where('item_id', $cmdHistory->item_id)
                                ->where('initiated', 0)
                                ->where('execute_once_on_any_server', 1)
                                ->whereIn('status', [CommandHistory::STATUS_QUEUE, CommandHistory::STATUS_PENDING])
                                ->where('server_id', '!=', $cmdHistory->server_id)
                                ->get();

                            // Deleting all pending commands for the same payment and item
                            foreach ($cmdHistories as $cmdHistoryToDelete) {
                                CmdQueue::where('commands_history_id', $cmdHistoryToDelete->id)->delete();
                                $cmdHistoryToDelete->delete();
                            }
                        }
                    }

                    // Remove the current command from the queue
                    $cmd->delete();

                    $processed[] = $id;
                    $results[] = [
                        'id' => $id,
                        'status' => true
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'id' => $id,
                        'status' => false,
                        'error' => 'Failed to process command'
                    ];
                }
            } else {
                $results[] = [
                    'id' => $id,
                    'status' => false,
                    'error' => 'Command not found'
                ];
            }
        }

        return json_encode([
            'status' => !empty($processed),
            'processed' => $processed,
            'results' => $results
        ]);
    }
}
