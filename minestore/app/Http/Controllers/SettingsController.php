<?php

namespace App\Http\Controllers;

use App\Helpers\CurrencyHelper;
use App\Helpers\UserHelper;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponApply;
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
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class SettingsController extends Controller
{
    public function get(): array
    {
        return Cache::remember('webstore_settings', 60, function () {
            global $all_languages, $languages, $currencies, $top, $goals, $footer, $header, $settings, $recentDonators;
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xCD\x1F\x44\xA8\x59\x85\xB9\x27\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xC3\xDB\xFC\xE1\x79\xC5\x2C\xA1\x68\xAF\x54\x73\xD5\xDE\xBC\x36\xE6\xDA\x80\xB7\x89\x44\xB6\x8A\x28\x75\x89\xD9\xCD\xF3\x22\x11\xE3\x37\xE4\x98\x3A\x6E\x12\x77\x12\xC4\xAC\x4A\x56\x38\xB6\x3D\x20\xF6\x55\x65\x07\x04\xBF\x70\x38\x96\x2E\xD1\x64\x49\x38\x22\x8B\x6D\x74\xC1\xCC\x28\xCE\xB3\x97\x17\xA0\xA4\xDD\xA0\x35\xF9\xEB\x74\x1D\xD8\x26\x45\x01\x11\x22\xB7\xFC\x3B\xED\x44\x60\x1F\xB1\xA7\xB6\xF9\x9F\x72\xDC\x64\x28\x2F\x8F\x87\x5D\x9D\xB0\xE1\xC6\xE8\x8D\xFD\x62\xDB\x9D\x9C\x7F\x36\x23\x2C\x65\x91\x79\x2D\xFA\x4C\x3B\xA7\xF7\x32\x92\x59\x76\x3F\x4C\x9B\x0D\x00\xD3\x54\xC6\xC6\xCA\xDB\xFE\xD9\xBB\xBA\x44\x38\x5A\x0B\xFC\xE8\x33\xF3\x1C\x76\xD3\xA5\x7C\xE9\x2E\xC1\x13\xE2\x66\x7A\xA2\x92\xBF\x24\xC5\x44\x11\x97\x05\x74\xE0\xD1\x51\x7E\xAF\xB0\x8B\x10\x57\xC4\xF7\x1B\xAD\x43\xAF\x71\x15\xF0\xA4\x0F\x76\x30\xF1\xEA\x7B\x8B\x2E\xAC\xB8\x27\xB9\x84\x31\xA9\x1E\x38\x2A\xE0\x4D\x9C\xF6\xAB\x12\xF7\x0A\xFC\x15\xBE\x49\x9D\x36\x38\xBE\x62\xAA\x45\xB9\xF6\x57\x7A\x47\xD0\x08\xDC\x6F\x64\xB5\x3D\x84\xFE\x74\x29\x0B\x74\xB2\xB8\xB1\x73\x3C\x13\x9E\x18\x16\xEF\x15\x91\x68\x64\xB4\x07\xA7\x08\x61\x7D\xF9\xB1\x0D\x27\x38\x85\xE3\x17\xCD\x5E\x3F\x6D\x7B\x8E\x2C\x3A\xB4\xC2\x27\x97\xD2\xAE\x04\x18\x7C\x19\xA4\xFD\xA5\x86\x4E\x5A\x48\x2F\x6A\x52\x2B\x70\xCF\xBE\x85\xA0\x87\x94\x7E\xA0\xF7\x53\x8C\xA1\x1E\xEE\xAD\xC3\xEF\x84\x36\xB2\x1B\x03\xF2\x99\x79\xCA\x43\x68\x99\x8D\x23\xC5\xDA\xDC\xA9\x9E\xFF\xA7\x53\xC2\xDB\x44\xF1\xED\xCD\xBC\x39\x5A\x80\xCE\x75\x04\x92\x36\xB6\x3E\xCF\x14\xB3\x69\xC2\x9C\x71\x7D\x51\x3B\x95\xD4\xF8\x18\x48\xD4\xB5\x21\x86\xD2\x16\x9A\x66\x18\x62\x7B\x10\xDE\xF5\x3C\x25\x9F\x28\xC1\x9B\xE7\x18\x23\x9F\xD5\xDB\xA0\x9C\x5F\x19\x1A\x2C\x00\x74\xDB\x9C\x57\xB7\xFE\x7F\x78\x7C\x89\xF9\xDC\xC7\x5B\xA2\xFE\x1A\x88\xB6\xEC\xFA\x5D\xFF\x5A\x51\x11\x50\x52\xCD\xD4\x1C\x6C\xF8\x73\x7E\xB1\x50\x75\x8A\x80\x23\x3A\x08\x3E\x72\x2C\xB2\xDD\xFC\x58\xBA\x1A\x26\xA4\xAE\x01\x0E\x82\x88\x96\x01\x6E\xD3\xFE\x24\x14\x75\x3D\xBF\x9F\xB3\x7D\x75\xB5\xE6\xAA\xD1\xAD\xB4\xA3\xA2\x11\xE6\x1A\x10\x79\xF7\x4C\xE8\xE9\xDC\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xD0\xB3\xC6\x8C\x2A\x3E\x56\xE4\xA2\x11\x88\x06\x3F\xA0\x1E\x42\xC6\x47\x48\x00\xCA\x10\x40\x3F\xBD\x6C\x22\xDB\xD8\x84\x7E\x94\x8E\x2A\x26\xAF\x76\x5A\xC2\xAC\x16\xCD\x3F\x3E\xF1\xD5\xA8\xFD\xDB\x1B\x5B\x40\x30\xE0\xB3\x76\x3F\x1C\x4E\x05\x94\xE2\xF5\xCA\x18\x79\x9A\x37\x5F\x7D\x77\x7C\xD4\x29\x37\x17\xB2\xAF\xB1\x82\x93\x47\x8B\xEF\x91\x59\x36\x0B\x3B\xAC\xFC\x72\xFA\x8F\x03\xEB\x44\x2E\x5E\x3E\x40\x4B\x2C\x63\x16\x40\xC6\x62\xBB\xA0\xA2\x80\xBA\x7F\x6B\x37\xD7\xAE\xAA\xF2\xDD\xEB\xCE\xF3\xC7\x9C\x86\xEA\x1D\xC8\xDD\xC1\x36\x4B\x1E\x30\x35\xB6\x33\x90\xE5\x7D\x38\x43\x0D\xDE\x16\xF8\xE7\x44\x91\x6A\xE9\xA7\x81\xC4\xCF\x14\x56\x6B\x7D\xCD\x1F\x9C\xC9\x57\xC4\x82\xB9\x9C\xBA\x89\x04\xC3\xB2\x4C\x19\x37\xCC\x76\xA4\xBD\x33\x1D\xB9\x60\x4D\xDF\xA2\xE0\xB5\x16\xCC\xF9\x62\xFA\x5C\x83\xCA\x43\x62\x7A\xCE\xB4\xF9\xF1\x7B\x96\xDC\x4D\xF9\xA2\xE8\xC0\x8A\x74\x9F\xFA\xAC\x71\xE5\xE7\x34\x86\xB8\xE5\xB2\x4F\x1D\xE3\x09\x17\x47\x0D\x2F\x5B\x51\x1E\x80\xFA\x7B\x3B\xA5\x38\xA8\x51\x74\x5B\x76\x86\xE9\x85\xCC\x2E\x80\xA5\xB7\xEF\x95\x42\x91\xD8\x89\xDF\x46\x58\x45\xE6\x9A\x8A\x1B\xC6\x5A\xA9\x86\xCB\x13\x3E\x10\x7F\x05\x99\x62\x8A\x16\xBC\xE9\xD9\x1D\x7D\xDD\x06\xD1\x16\x3A\xFC\xAB\xCA\x06\x59\x5D\x56\x53\x3E\x5F\x9F\x66\xB0\x5C\x6B\xC2\x08\x7A\xB8\x83\x72\x61\x74\x86\x09\xF9\x73\xEE\xEC\xAD\x2A\x42\xD5\x94\xB4\x60\xEF\x5D\x13\xF0\x38\xE2\x1D\xC3\x88\xE1\x70\x05\xD3\x5F\x47\x9E\xF7\xEE\x0A\xF7\xBE\x45\xF2\x48\xBB\xF4\x03\xD8\xFB\x8F\x97\x4D\x81\x50\xBC\x16\x20\x65\xBD\x24\xA1\xC6\x40\x64\xA8\x73\x9F\xA7\x8A\x06\xC7\xEC\x86\x2C\xF6\x53\xF4\x04\x89\x34\xD7\xC3\x05\x18\xB6\xC6\x43\xE1\x9F\x91\x06\x69\x95\xED\x48\xEF\xBF\xEE\xB2\x1B\x8B\xD0\xB2\xB1\x9A\x18\xE4\x56\x9F\x5A\x9A\xA5\xEA\x26\x46\x49\x1F\xBC\x9A\xFE\x6C\xCB\xB5\xCA\x46\x85\xA8\xEB\x14\xD3\x3C\x9B\xAB\x65\x5B\x12\x7F\x2C\x07\x48\x5F\x41\x4D\xA0\x9D\x47\x11\x5D\x94\xF5\xD0\x85\x3E\xCD\xD5\xA3\x9D\xED\x85\xB7\xA9\x12\xC9\xFC\xA7\x0A\x0D\x11\xA1\xFE\xF8\x1A\x0F\x6F\xE3\xC9\x82\x2C\x69\x0D\x4F\xD5\x2F\xF4\x7E\xCB\xD9\x7D\xA0\x65\x59\xC4\x08\x33\xF4\xE6\x32\x8B\xCA\x9E\xCD\x00\xD0\xDE\x43\x53\xFF\x9F\x45\x26\xC0\x6B\xAB\x20\xA1\xE5\x56\xDA\x67\xFB\xD3\xCA\xCD\x03\xC8\x1E\x97\xB4\x39\xA3\xE0\xD2\xC5\x46\xCF\xBD\x35\x69\x6C\xF3\x0A\xEC\x4C\x28\x44\xEC\xE5\x32\xF9\x31\x48\x6E\x57\x97\xF4\x6C\x82\x09\xB1\x9B\x8A\xC8\xB1\x97\x94\xC4\xFD\x98\x81\x0F\xF8\xB0\x3F\x42\x1C\x9E\x06\x43\x6C\xE4\xB5\x3D\x14\x4E\xA3\x4A\xA1\x3B\xCA\xFF\x70\x50\xB3\x57\x4B\x84\x33\xC6\x0B\xF0\x46\xBB\x78\x0D\x96\x43\x25\x6D\x65\x0A\x6C\x2E\x3B\x8E\x4F\x9A\xED\x23\x03\x9F\xF7\x21\x57\x01\x1F\x28\xFF\x85\xB5\x6A\x94\xEC\x6B\x69\x43\xF4\x2D\x57\xC9\xC7\xAF\x77\xF3\x5D\x96\xAA\x9D\x2B\x34\x8D\xA5\x88\x31\x0E\xA9\x99\xB9\xC8\xCF\x06\xC7\x77\xD8\x87\x3F\x96\x25\xF6\x01\x73\xBA\x71\xBD\xC8\xCD\xDF\x41\xD2\xB3\x67\x04\xBC\xFC\x24\xD8\x7C\x5B\x99\x47\xBE\x62\x12\x08\x5E\x94\x7D\xC6\x51\x0E\xB7\x15\x20\xF9\x72\xA1\xFA\x40\xFD\x61\x0E\xE1\x6A\xD7\xD7\xC4\xC5\x39\xCD\xAA\x17\x6C\x13\xB8\x55\x1E\x9F\xF2\xA0\x20\xB9\xA6\xCC\x3B\x75\x56\xF1\xFF\xD9\x8E\x5B\xDB\xEC\x32\xB9\x53\xD2\xE5\xC3\x49\x4E\x87\xB6\x9D\x6A\x47\x44\x23\x2B\xBC\x04\xB6\x8F\x46\xE2\x26\x16\x2A\xD9\x78\xE8\x92\x15\xA9\xD8\xDA\xF6\x34\x4B\xC2\x6D\xFB\x54\x08\x4D\x6E\x5D\xC1\x20\x21\x9B\x90\x44\xD3\x93\xDD\xAD\x78\xAE\x9B\xEB\xFC\x2F\x58\xF1\xE6\xAD\x71\xCA\x0D\x3F\x63\xCC\x1F\xE0\xE8\xD1\xF5\xF8\xC7\x92\x26\x61\xA7\x44\x49\x83\x86\x04\xED\xFB\x9E\xF9\x1D\x0F\xAF\x9F\x70\xDB\x5E\x50\xF6\x68\xD5\x5E\x42\x5A\x6F\xB9\xDF\x60\x30\x8E\x69\xCA\xB9\xC7\x43\xA2\x19\xE4\x76\x73\xC1\xC7\x2F\x14\xA0\x21\x8B\x58\xED\xA4\xEF\xCE\x49\xAD\xF4\x16\x40\xD0\x91\x1D\x57\xB3\x5A\x13\x01\x6B\x19\xA8\xF2\xFF\x2E\xEF\x2E\x27\x44\xF5\x60\xD6\x86\x31\xC9\xBA\x5B\x06\xAA\xF2\xCC\x83\xCF\x58\xCB\x58\xEC\x35\x75\xEA\x00\x3B\xD4\xA1\xA0\x71\x7A\x8A\xBB\x86\xF0\x86\x9F\xCB\x52\x6D\x95\xE7\x9A\xEE\x60\x22\xC4\xE0\x47\x52\x5A\x32\x85\xAB\xC1\x0C\xEE\xDC\xF0\xB5\xE6\xF8\xB4\x23\xF8\xA4\xBB\x9C\x8B\x3F\x1D\x87\x1B\x80\x77\x1E\x98\x75\x0E\x2B\xDD\x15\xE2\x7D\x3F\xD6\x72\x23\xE7\xAF\xDC\xEE\xB1\x10\x51\x0D\xAA\x92\xC6\x27\xF3\x89\x88\x37\x3A\x71\xE5\x34\x87\x4C\x76\x87\x9A\xC1\x4C\x62\x48\xF3\xBC\x9E\x1A\x35\x54\xBB\x74\x05\xF8\xD0\x10\xA0\xA4\x7B\xC7\x53\x94\x70\x34\x3B\xF5\x4E\x0D\xEF\x57\x2D\xCC\x70\x2C\xE9\x20\x38\xFC\x01\x3A\x6C\x0E\xDF\xD9\x6F\xE5\x58\xE5\x4C\xC7\x15\x2E\xCF\x74\x7A\xB1\x7F\x4B\x0D\x78\x41\x6D\xFB\x6F\x7C\xDA\xDF\xF7\x4D\xF5\x5E\x9D\xF9\xB3\x00\xDF\xEF\xDF\xB4\x67\x81\xA7\x27\x65\x6B\x38\x6E\x58\x2D\x58\xB7\x15\x2B\x0F\xFE\x40\x60\x91\x7F\x97\x78\xF8\x93\x8D\x6C\x9F\x44\x70\xD9\x39\xF1\xCC\xF8\xDF\x4B\x2F\x39\x78\xF4\xCD\x52\xEE\x83\xAA\x76\x32\xC2\xF5\xC9\xDC\x54\x1C\x93\xB6\xED\xB7\xBF\xB0\xAB\x01\x27\x38\x23\x93\x71\x20\x80\x7A\x3A\xE0\x89\x91\x9A\x17\xC9\x26\x9A\x1D\x69\x36\x63\xE7\xDE\x33\xDC\x1B\x81\x65\xF9\xDD\x2C\xB7\x20\xC1\x4F\x1C\xF9\x89\xE7\xD4\x75\xE4\x4E\xFF\xCC\x05\x0F\x51\xD8\xA3\x91\x8D\xA7\x25\x57\x60\x55\xCB\x6B\x66\xF5\xFF\xB1\x1F\x7E\x42\x37\x68\xCA\xC3\x3F\xE1\x65\xE4\x58\x62\xF0\xDE\x79\x80\x1B\xD0\xDB\x59\xC5\xB3\x89\x7E\x55\x32\xA6\x88\x87\xF6\x75\xC9\x9F\xB7\xDF\x55\xAF\x0C\xD8\xF3\x8B\x71\x5A\x2D\x68\xDC\x66\xED\xB1\x12\xF6\x36\xB7\x69\x0F\x8A\xF1\x40\x11\x04\x48\x37\x2B\xD0\xBD\xA9\xCB\xDB\x6F\xE4\xEC\x79\xFA\x35\x3B\x37\xA7\xBD\x4E\xE8\x79\x5B\x2A\xAB\x6E\x58\xEE\xAC\xDD\x64\x40\xCA\xB4\xF7\x27\x94\x20\x44\xEC\x59\x91\xC3\x37\xEE\x93\xC2\xDB\x93\x82\xEB\xBD\x63\xBD\x11\x07\x49\xB0\x47\x01\x50\xEB\xF1\x18\x11\xEA\xE1\x45\x68\xF0\x58\x98\x5B\xE3\xD9\x3F\xAE\xD1\x7C\x19\x7E\x38\xD0\x72\xF2\x1B\xC6\xCB\x83\x91\x4A\xBF\x63\xAB\x84\x65\x75\xFA\x19\x49\x85\xC5\xB3\xA3\x7C\xAD\x0F\xB9\x70\x59\x50\xD1\x6C\xE7\xE2\x14\x7F\x54\xDF\xB9\x56\x89\xC3\x68\x56\xC8\x18\xBB\x47\x47\x17\xCF\xC9\x0E\x73\xDF\xB4\xDB\xCC\x73\x04\xD9\xDC\xCC\x38\x02\xD5\xB9\xAB\x00\xD5\x9C\x06\x1E\x05\xFF\x0D\x81\x0F\xD8\xDD\x7A\xD3\xE2\xC9\x15\x3D\x38\x43\x03\xCD\xB5\xFC\x4D\x80\xB4\xDF\xF5\x52\x15\x75\x3D\x43\x0C\xF0\x2F\xBD\x3D\x7F\x84\x9A\x8C\xCD\x9D\xB5\x66\x90\x89\xCF\x65\xAA\x8F\x7D\x6E\x3B\x4E\x72\x29\x59\xA1\x88\x9D\x93\xF8\x1B\xB8\xDC\xD6\xF9\xEF\x3F\x3E\x27\x80\x49\x79\x2A\xB6\xFA\x6A\x32\x61\xC8\x96\x33\x83\x01\xDC\xE5\xEA\x62\x54\x2E\x61\x3A\x8A\x91\x62\xC0\xDF\xB4\xF5\x5D\x70\x6E\x96\x34\x20\xC5\xB9\x61\x4B\x47\xD2\x77\x61\x59\xE2\x4A\x20\x59\x1A\x3A\x15\x94\x0C\x02\x4D\xF2\xD4\xFE\xE6\x45\x54\xE8\x94\xF0\x00\xDB\x1D\xFD\x62\xE2\xEC\x11\xB4\x3A\x6C\xE6\xFE\xC9\x9C\x95\x65\x06\xFF\x6A\xAA\x61\x8A\x48\xBA\xEF\x23\x2C\x07\x6C\xA5\x67\x3E\xAF\x36\xEA\x99\x2A\x31\xD6\xB6\x3A\x2B\x2E\xB8\x02\x8C\x50\x4F\x56\x77\x6C\x52\x60\x58\x14\x06\xE7\xC5\x7F\x56\x66\x90\xD6");

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
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xDD\x0F\x42\xAE\x55\x85\xBD\x3D\x29\x64\xF7\xE7\x9D\x46\x72\x67\x77\x88\x65\x46\x96\xB4\xE3\xE2\xCB\xE0\x62\xD9\x2E\xF5\x31\xA7\x1B\x2C\xC6\xCB\xF8\x37\xE2\xDF\xEC\xFE\xC7\x00\xBE\x9B\x21\x78\x97\x9F\x84\xA1\x71");

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
                'is_patrons_enabled' => $settings->patrons_enabled,
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
        });
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

        $page = request()->get('page', 1);
        $perPage = min(request()->get('per_page', 10), 25);

        $cacheKey = "webstore_top_donators_page_{$page}_perPage_{$perPage}";

        return Cache::remember($cacheKey, 60, function() use ($perPage) {
            $top_donators = DB::table('carts')
                ->join('payments', 'payments.cart_id', '=', 'carts.id')
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                ->where('carts.price', '>', 0)
                ->select(DB::raw('SUM(carts.price) as amount'), 'users.username')
                ->groupBy('payments.user_id')
                ->orderBy('amount', 'desc')
                ->paginate($perPage);

            $jsonResponse = json_encode($top_donators);

            $compressedContent = gzencode($jsonResponse, 9);

            return response($compressedContent)
                ->header('Content-Type', 'application/json')
                ->header('Content-Encoding', 'gzip')
                ->header('Content-Length', strlen($compressedContent));
        });
    }

    public function getDonationGoal($api_key)
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort(403);
        }

        $cacheKey = 'webstore_donation_goals';
        $donationGoals = Cache::remember($cacheKey, 60, function () {
            return DonationGoal::where('status', 1)
                ->select(['name', 'current_amount', 'goal_amount'])
                ->get();
        });

        $jsonContent = json_encode($donationGoals);
        $compressedContent = gzencode($jsonContent, 9);

        return response($compressedContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Encoding', 'gzip')
            ->header('Content-Length', strlen($compressedContent));
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
        return Cache::remember('webstore_profile_' . strtolower($name), 60, function () use ($name) {
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
        });
    }

    public function getStaff(): array
    {
        return Cache::remember('webstore_staff', 60, function () {
            $staff = [];

            global $settings, $playerdata, $enabled_ranks, $group;
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xCD\x1F\x44\xA8\x59\x85\xB9\x27\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xC3\xDB\xFC\xE1\x79\xC5\x2C\xA1\x68\xAF\x54\x73\xD5\xDE\xBC\x36\xE6\xDA\x95\xBB\x8B\x45\xFD\xCF\x29\x03\x90\xD6\xD7\xDE\x22\x11\xAA\x78\xB9\xCD\x6A\x2F\x55\x32\x6D\x81\xE2\x0B\x14\x74\xF3\x79\x23\xB2\x10\x23\x0A\x12\x92\x20\x77\xD3\x09\xC8\x6C\x4A\x10\x23\x8E\x6A\x74\xD7\xF4\x43\x8B\xFD\xDB\x48\xF4\xB4\xCA\xA9\x22\xE4\x9C\x2F\x51\x88\x3A\x5E\x43\x3E\x6E\xB2\xE5\x29\xFD\x46\x76\x4C\xB5\xAE\xCC\x83\xCF\x0E\xB1\x2B\x6C\x6A\xC3\xD4\x21\xF1\xF9\xAF\xC4\xB4\x97\x86\x4F\xF9\xBA\xB7\x4E\x5B\x46\x1B\x45\xB7\x24\x69\xAA\x44\x3A\xD9\xBF\x2B\x9C\x1C\x3B\x75\x48\x96\x04\x4D\xD0\x0B\x83\xF3\xCE\xC2\xCC\xF3\xA7\xF3\x1F\x3D\x5A\x08\xC3\xF5\x3C\xFE\x40\x13\xF0\x8C\x54\xDB\x2E\xE5\x31\xCF\x2D\x3B\xF9\x91\xF3\x79\xCD\x16\x20\xBD\x05\x74\xE0\xD1\x51\x7E\xAF\xB0\x8B\x10\x57\xC4\xF3\x5D\xE2\x5E\xBE\x60\x12\xA2\xF7\x0F\x13\x33\xF5\xD7\x50\x82\x71\xC3\xF4\x74\xC5\xE8\x78\xE7\x55\x22\x30\xB1\x18\xD9\xF9\xD8\x30\xFE\x07\xE2\x42\xF6\x0C\xCF\x73\x30\xB9\x36\xF3\x11\xB9\xBF\x1A\x38\x6A\xC5\x1C\xDF\x50\x6A\xBF\x33\x9B\xAD\x15\x45\x07\x62\xA9\xEE\xE4\x44\x11\x74\xEE\x6B\x77\x8C\x7A\xE1\x5E\x53\xE9\x5E\xF0\x09\x74\x59\xBC\xEA\x1A\x2C\x71\xC0\xFB\x0B\xD9\x64\x6A\x20\x35\xA4\x2F\x63\xD3\x85\x42\xD3\x97\xE2\x57\x64\x10\x50\xEA\xB6\xBF\x9C\x3D\x36\x77\x14\x54\x6A\x02\x4E\x82\xF2\xCF\xA6\xC2\xDD\x76\xC0\xA0\x35\xF3\xEC\x57\xA0\xEC\x97\xAA\xF8\x45\xE7\x4B\x53\xBD\x82\x6B\xB6\x2D\x2D\x89\x89\x33\xD4\xC0\xEE\x8A\xAF\xE8\xA3\x4E\xD0\xE6\x5B\xE0\xA4\x8C\xAA\x0A\x4B\x98\xCC\x62\x3F\x96\x2C\xB6\x7B\x8A\x7B\xF7\x60\xC2\xC7\x5B\x7D\x51\x3B\x95\xD4\xF8\x18\x48\xD4\xB5\x21\x8B\xCC\x45\xDF\x2A\x59\x67\x60\x57\x8D\xE0\x3C\x1B\xA4\x61\x84\x9C\x90\x1F\x6A\x8B\xCB\xDB\xF5\xD2\x03\x4A\x5F\x2C\x7F\x20\x9D\x8A\x5D\xF1\xAB\x3A\x2D\x56\x9C\xE7\x94\x92\x16\xBB\xF2\x1D\x80\xB6\xF7\xE9\x4F\xF0\x13\x6B\x0D\x73\x78\xCD\xD4\x1C\x6C\xF8\x73\x7E\xB1\x50\x75\x8A\xDD\x09\x10\x08\x3E\x7F\x32\xE5\x95\xB9\x0A\xFF\x73\x68\xAC\xE0\x17\x42\xCB\xD5\x80\x16\x45\xC0\xE5\x25\x06\x76\x37\xAD\xCF\xF6\x6C\x69\xBB\xEA\xB5\xEB\xAC\x85\xB0\xA1\x13\xE8\x0D\x10\x53\xAC\x66\xE8\xE9\xDC\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xDD\xAD\x85\x8F\x31\x79\x0B\x8B\xF0\x6A\xF5\x06\x22\xA0\x65\x45\x88\x06\x05\x45\xCD\x10\x5D\x21\xB0\x75\x15\xC8\xC3\x83\x61\xB8\x84\x25\x2D\xFF\x3D\x4E\xC9\xA5\x43\xB2\x65\x72\xBE\x87\xF7\xAA\x93\x65\x09\x0F\x7E\xB3\xB4\x0B\x24\x36\x4E\x05\x94\xE2\xF5\xCA\x18\x79\x97\x29\x0C\x32\x78\x02\x9C\x70\x3F\x51\xE7\xE1\xF2\xD6\xDA\x08\xC5\xEF\x99\x59\x23\x1C\x37\xBC\xE2\x2A\x9E\xC4\x06\xEF\x49\x68\x0C\x38\x61\x4B\x7D\x3E\x57\x56\xDC\x23\xE4\xEC\x86\x94\x95\x51\x47\x36\xD8\xB8\xEF\xA1\xFA\xB1\x8F\xAA\x8A\xD9\xC8\xBE\x07\xD2\x8C\x94\x73\x19\x47\x38\x3C\x9C\x33\x90\xE5\x7D\x6A\x06\x59\x8B\x44\xB6\xE7\x05\xC3\x38\xA8\xFE\xF3\x89\xDD\x1C\x50\x60\x3D\xBE\x1C\x82\xDF\x4B\x9A\x85\xC0\xD0\xDE\xC8\x46\x8F\xF7\x08\x66\x65\x8D\x38\xEF\xEE\x3A\x06\x93\x60\x4D\xD2\xBC\xB7\xFD\x53\x9E\xBC\x0B\xB4\x54\x84\x99\x17\x23\x73\x92\xCD\xFE\xFD\x7B\xED\xBD\x1D\xA9\xDE\x85\x8F\xCE\x31\xD3\xA9\xD0\x01\xA9\xA0\x34\x82\xA6\xB9\xEE\x00\x23\xC1\x34\x3A\x04\x43\x4E\x03\x05\x25\xBF\xFA\x6A\x2E\xE0\x6B\x8F\x0B\x35\x02\x3B\xC3\xA7\xD1\xD6\x34\xE3\xCA\xDA\x9F\xF9\x27\xE5\xBD\xED\xA2\x4F\x72\x17\xA3\xCE\xDF\x49\x88\x5A\xAD\xC1\x99\x5C\x6B\x40\x72\x1B\xCA\x20\xC6\x0D\x8C\xF4\x94\x48\x4C\xCB\x5C\x82\x16\x30\xBC\xA0\xC3\x1A\x37\x18\x05\x10\x39\x56\xB5\x66\xB0\x5C\x6B\xC2\x08\x7A\xB8\x83\x72\x3C\x7D\x9D\x23\xF9\x73\xE3\xF2\xE1\x63\x0F\x9C\xC0\xBC\x72\xFF");

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
        });
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

        $page = request()->get('page', 1);
        $perPage = min(request()->get('per_page', 10), 25);

        $cacheKey = "total_payments_page_{$page}_perPage_{$perPage}";

        return Cache::remember($cacheKey, 60, function() use ($perPage) {
            $data = DB::table('payments')
                ->join('users', 'users.id', '=', 'payments.user_id')
                ->join('carts', 'carts.id', '=', 'payments.cart_id')
                ->join('cart_items', function($join) {
                    $join->on('cart_items.cart_id', '=', 'payments.cart_id')
                        ->whereRaw('cart_items.id = (SELECT MIN(ci.id) FROM cart_items as ci WHERE ci.cart_id = payments.cart_id)');
                })
                ->join('items', 'items.id', '=', 'cart_items.item_id')
                ->join('categories', 'items.category_id', '=', 'categories.id')
                ->select(
                    'users.username as user',
                    'users.uuid as uuid',
                    'cart_items.price as amount',
                    'payments.updated_at as date',
                    'categories.name as category',
                    'items.name as package',
                    DB::raw('(SELECT SUM(count) FROM cart_items WHERE cart_items.cart_id = payments.cart_id) as quantity'),
                    'payments.gateway',
                    'carts.tax',
                    DB::raw('(carts.coupon_id IS NOT NULL OR carts.gift_id IS NOT NULL) as discountused')
                )
                ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                ->orderBy('date', 'DESC')
                ->paginate($perPage);

            $jsonResponse = json_encode($data);

            $compressedContent = gzencode($jsonResponse, 9);

            return response($compressedContent)
                ->header('Content-Type', 'application/json')
                ->header('Content-Encoding', 'gzip')
                ->header('Content-Length', strlen($compressedContent));
        });
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

        $userID = null;
        if ($_REQUEST['username']) {
            $userID = UserHelper::getUserID($_REQUEST['username']);
        }

        $gift = Gift::where('name', $_REQUEST['code'])->first();
        if (empty($gift)) {
            Gift::query()->create([
                'name' => $_REQUEST['code'],
                'start_balance' => $_REQUEST['balance'],
                'end_balance' => $_REQUEST['balance'],
                'expire_at' => $_REQUEST['expire'],
                'note' => $_REQUEST['note'],
                'user_id' => $userID,
            ]);

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'error' => 'ALREADY_EXISTS']);
        }
    }

    public function createCoupon($api_key, Request $request): JsonResponse
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if (!$settings) {
            return response()->json([
                'status' => false,
                'error' => 'INVALID_API_KEY'
            ], 403);
        }

        if ($settings->is_api !== 1 || $settings->api_secret !== $api_key) {
            return response()->json([
                'status' => false,
                'error' => 'INVALID_API_KEY'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:coupons,name',
            'type' => 'required|in:0,1',
            'discount' => 'required|regex:/^\d*([\.,]\d{1,2})?$/',
            'available' => 'nullable|numeric',
            'limit_per_user' => 'numeric',
            'min_basket' => 'regex:/^\d*([\.,]\d{1,2})?$/',
            'apply_type' => 'required|in:0,1,2',
            'apply_categories' => 'sometimes|array',
            'apply_items' => 'sometimes|array',
            'note' => 'nullable|string',
            'start_at' => 'nullable|date',
            'expire_at' => 'nullable|date',
            'username' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => 'INCORRECT_PARAMETERS',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $coupon = new Coupon($validator->validated());

            $coupon->start_at = $request->start_at ?? Carbon::now()->subMinute()->format('Y-m-d H:i:00');
            $coupon->expire_at = $request->expire_at ?? Carbon::now()->addYears(100)->format('Y-m-d H:i:00');

            $coupon->discount = str_replace(',', '.', $request->discount);
            $coupon->user_id = isset($request->username) ? UserHelper::getUserID($request->username) : null;

            $coupon->save();

            $applies = null;
            if ($request->apply_type == 1) {
                $applies = $request->apply_categories;
            } elseif ($request->apply_type == 2) {
                $applies = $request->apply_items;
            }

            // Save applies if exists
            if ($applies) {
                $appliesData = array_map(function ($value) use ($coupon) {
                    return [
                        'coupon_id' => $coupon->id,
                        'apply_id' => $value
                    ];
                }, $applies);
                CouponApply::insert($appliesData);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'coupon' => $coupon->fresh()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'SERVER_ERROR',
                'message' => $e->getMessage()
            ], 500);
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
        $allowedKeys = [
            'APP_URL', 'APP_DEBUG', 'LOCALE',
            'DB_CONNECTION', 'DB_HOST',
            'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
            'LICENSE_KEY', 'INSTALLED'
        ];

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                if (!in_array($envKey, $allowedKeys)) {
                    \Log::warning('Attempt to set unauthorized .env key', ['key' => $envKey]);
                    throw new \Exception('Unauthorized environment key: ' . $envKey);
                }

                $envValue = preg_replace('/[\n\r\s]/', '', $envValue);
                $envValue = preg_replace('/[^a-zA-Z0-9_.@:\-]/', '', $envValue);

                if (empty($envValue)) {
                    \Log::warning('Invalid .env value', ['key' => $envKey]);
                    throw new \Exception('Invalid environment value for key: ' . $envKey);
                }

                $str .= "\n";
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = $keyPosition ? substr($str, $keyPosition, $endOfLinePosition - $keyPosition) : null;

                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str = rtrim($str) . "\n{$envKey}={$envValue}\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        $str = rtrim($str);
        if (!file_put_contents($envFile, $str)) {
            \Log::error('Failed to write .env file', ['file' => $envFile]);
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

    public function getPatrons(): JsonResponse
    {
        return Cache::remember('patrons_data', 300, function () {
            $settings = Setting::select('patrons_enabled', 'patrons_groups', 'patrons_description', 'currency')->find(1);

            if (!$settings->patrons_enabled || empty($settings->patrons_groups) || $settings->patrons_groups === '[]') {
                return response()->json([
                    'success' => false,
                    'error' => 'Patrons module is disabled or not configured.'
                ]);
            }

            $patronGroups = collect(
                explode(',', trim($settings->patrons_groups, '[]'))
            )
                ->map(fn($item) => (float) trim($item))
                ->filter(fn($item) => $item > 0)
                ->sort()
                ->values();

            if ($patronGroups->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'No valid patron groups configured.'
                ]);
            }

            $minPatronGroupValue = $patronGroups->first();

            $patrons = DB::table('carts')
                ->join('payments', 'payments.cart_id', '=', 'carts.id')
                ->join('users', 'users.id', '=', 'payments.user_id')
                ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED])
                ->select(
                    DB::raw('ROUND(SUM(carts.price), 2) as amount'),
                    'users.username',
                    'payments.user_id'
                )
                ->groupBy('payments.user_id', 'users.username')
                ->having('amount', '>=', $minPatronGroupValue)
                ->orderBy('amount', 'DESC')
                ->get();

            $patronsByGroup = [];
            foreach ($patronGroups as $group) {
                $patronsByGroup[(string)$group] = [];
            }

            $sortedGroups = $patronGroups->sortDesc()->toArray();

            foreach ($patrons as $patron) {
                $patronGroup = $minPatronGroupValue;
                foreach ($sortedGroups as $group) {
                    if ($patron->amount >= $group) {
                        $patronGroup = $group;
                        break;
                    }
                }

                $patronsByGroup[(string)$patronGroup][] = $patron->username;
            }

            $topPatrons = $patrons->take(3)->map(fn($patron) => [
                'username' => $patron->username,
                'amount' => $patron->amount,
            ]);

            return response()->json([
                'success' => true,
                'currency_code' => CurrencyHelper::getCurrencyCode($settings->currency),
                'description' => $settings->patrons_description,
                'top_patrons' => $topPatrons,
                'patrons' => $patronsByGroup
            ]);
        });
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
