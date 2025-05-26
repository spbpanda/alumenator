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
use App\Services\PayNowIntegrationService;
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
    protected PayNowIntegrationService $paynowService;

    public function __construct()
    {
        $this->paynowService = App::make(PayNowIntegrationService::class);
    }

    public function get(): array
    {
        return Cache::remember('webstore_settings', 60, function () {
            global $all_languages, $languages, $currencies, $top, $goals, $footer, $header, $settings, $recentDonators;
            zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x02\x80\x43\x7A\x21\xCD\x1F\x44\xA8\x59\x85\xB9\x27\x6C\x2A\xF7\x9B\xCD\x77\x5E\x5A\x44\xA1\x6F\x4E\x80\x84\xC3\xDB\xFC\xE1\x79\xC5\x2C\xA1\x68\xAF\x54\x73\xD5\xDE\xBC\x36\xE6\xDA\x80\xB7\x89\x44\xB6\x8A\x28\x75\x89\xD9\xCD\xF3\x22\x11\xE3\x37\xE4\x98\x3A\x6E\x12\x77\x12\xC4\xAC\x4A\x56\x38\xB6\x3D\x20\xF6\x55\x65\x07\x04\xBF\x70\x38\x96\x2E\xD1\x64\x49\x38\x22\x8B\x6D\x74\xC1\xCC\x28\xCE\xB3\x97\x17\xA0\xA4\xDD\xA0\x35\xF9\xEB\x74\x1D\xD8\x26\x45\x01\x11\x22\xB7\xFC\x3B\xED\x44\x60\x1F\xB1\xA7\xB6\xF9\x9F\x72\xDC\x64\x28\x2F\x8F\x87\x5D\x9D\xB0\xE1\xC6\xE8\x8D\xFD\x62\xDB\x9D\x9C\x7F\x36\x23\x2C\x65\x91\x79\x2D\xFA\x4C\x3B\xA7\xF7\x32\x92\x59\x76\x3F\x4C\x9B\x0D\x00\xD3\x54\xC6\xC6\xCA\xDB\xFE\xD9\xBB\xBA\x44\x38\x5A\x0B\xFC\xE8\x33\xF3\x1C\x76\xD3\xA5\x7C\xE9\x2E\xC1\x13\xE2\x66\x7A\xA2\x92\xBF\x24\xC5\x44\x11\x97\x05\x74\xE0\xD1\x51\x7E\xAF\xB0\x8B\x10\x57\xC4\xF7\x1B\xAD\x43\xAF\x71\x15\xF0\xA4\x0F\x76\x30\xF1\xEA\x7B\x8B\x2E\xAC\xB8\x27\xB9\x84\x31\xA9\x1E\x38\x2A\xE0\x4D\x9C\xF6\xAB\x12\xF7\x0A\xFC\x15\xBE\x49\x9D\x36\x38\xBE\x62\xAA\x45\xB9\xF6\x57\x7A\x47\xD0\x08\xDC\x6F\x64\xB5\x3D\x84\xFE\x74\x29\x0B\x74\xB2\xB8\xB1\x73\x3C\x13\x9E\x18\x16\xEF\x15\x91\x68\x64\xB4\x07\xA7\x08\x61\x7D\xF9\xB1\x0D\x27\x38\x85\xE3\x17\xCD\x5E\x3F\x6D\x7B\x8E\x2C\x3A\xB4\xC2\x27\x97\xD2\xAE\x04\x18\x7C\x19\xA4\xFD\xA5\x86\x4E\x5A\x48\x2F\x6A\x52\x2B\x70\xCF\xBE\x85\xA0\x87\x94\x7E\xA0\xF7\x53\x8C\xA1\x1E\xEE\xAD\xC3\xEF\x84\x36\xB2\x1B\x03\xF2\x99\x79\xCA\x43\x68\x99\x8D\x23\xC5\xDA\xDC\xA9\x9E\xFF\xA7\x53\xC2\xDB\x44\xF1\xED\xCD\xBC\x39\x5A\x80\xCE\x75\x04\x92\x36\xB6\x3E\xCF\x14\xB3\x69\xC2\x9C\x71\x7D\x51\x3B\x95\xD4\xF8\x18\x48\xD4\xB5\x21\x86\xD2\x16\x9A\x66\x18\x62\x7B\x10\xDE\xF5\x3C\x25\x9F\x28\xC1\x9B\xE7\x18\x23\x9F\xD5\xDB\xA0\x9C\x5F\x19\x1A\x2C\x00\x74\xDB\x9C\x57\xB7\xFE\x7F\x78\x7C\x89\xF9\xDC\xC7\x5B\xA2\xFE\x1A\x88\xB6\xEC\xFA\x5D\xFF\x5A\x51\x11\x50\x52\xCD\xD4\x1C\x6C\xF8\x73\x7E\xB1\x50\x75\x8A\x80\x23\x3A\x08\x3E\x72\x2C\xB2\xDD\xFC\x58\xBA\x1A\x26\xA4\xAE\x01\x0E\x82\x88\x96\x01\x6E\xD3\xFE\x24\x14\x75\x3D\xBF\x9F\xB3\x7D\x75\xB5\xE6\xAA\xD1\xAD\xB4\xA3\xA2\x11\xE6\x1A\x10\x79\xF7\x4C\xE8\xE9\xDC\xB0\x08\xFB\xD9\x7C\x5B\xD3\xB6\x9C\x8F\xB0\xD0\xB3\xC6\x8C\x2A\x3E\x56\xE4\xA2\x11\x88\x06\x3F\xA0\x1E\x42\xC6\x47\x48\x00\xCA\x10\x40\x3F\xBD\x6C\x22\xDB\xD8\x84\x7E\x94\x8E\x2A\x26\xAF\x76\x5A\xC2\xAC\x16\xCD\x3F\x3E\xF1\xD5\xA8\xFD\xDB\x1B\x5B\x40\x30\xE0\xB3\x76\x3F\x1C\x4E\x05\x94\xE2\xF5\xCA\x18\x79\x9A\x37\x5F\x7D\x77\x7C\xD4\x29\x37\x17\xB2\xAF\xB1\x82\x93\x47\x8B\xEF\x91\x14\x22\x4E\x70\xED\xE6\x6E\xD7\x93\x40\xB4\x40\x20\x55\x23\x7D\x59\x0E\x73\x01\x45\xDB\x21\xE3\xAD\xDD\x99\xB9\x73\x47\x21\xDE\xAE\xA8\xF5\xCC\xDE\xDB\xE2\xC5\x9D\xAD\xF0\x46\x90\xC0\xD1\x37\x11\x4E\x31\x3C\xC7\x19\x90\xE5\x7D\x38\x43\x0D\xDE\x16\xF8\xE7\x44\x91\x6A\xE9\xA7\x8C\xDE\xC8\x1C\x5B\x6D\x3A\xE1\x74\x80\xD4\x59\xC5\x85\xD9\x95\xEB\xA3\x04\xC3\xB2\x4C\x19\x37\xCC\x76\xA4\xBD\x33\x1D\xB9\x60\x4D\xD2\xBC\xB7\xFD\x53\xE5\xBB\x45\xF5\x19\xC1\x9E\x17\x3E\x30\x9B\xE0\x8E\x93\x7B\x99\xF8\x4F\xE4\x8D\x85\xCE\x80\x75\xD3\xCA\x9F\x4F\xE0\xF7\x2D\x8A\xB9\xFF\xFB\x52\x61\x82\x67\x26\x19\x41\x49\x0B\x1C\x7C\xED\xB2\x77\x2A\xBD\x3B\x87\x3B\x3A\x0D\x6B\x82\xFE\x9F\x99\x63\xED\x8D\x9D\x90\xAD\x62\xB7\xF0\xBE\xAF\x00\x34\x48\xB3\xC9\xCF\x1C\xBB\x56\x83\x86\xCB\x13\x3E\x10\x7F\x05\x99\x6F\x94\x59\xEE\xAD\x9C\x4F\x1F\x84\x0E\xD6\x5F\x05\xFC\xE9\x8B\x4C\x58\x1F\x05\x0D\x27\x56\xB2\x16\xDE\x5C\x1E\x91\x4D\x28\xB8\xE2\x35\x33\x31\xC3\x44\xBC\x3D\xB7\xF5\xED\x63\x08\xC9\x92\xF0\x75\xFF\x49\x27\xF0\x3F\xAA\x49\x97\xD8\xB2\x6A\x0A\xDC\x0F\x06\xC7\xB9\xA1\x5D\xF4\xE7\x45\xB8\x49\xE0\xB8\x4A\xFF\x90\xC8\xC5\x08\xC4\x1D\xF9\x58\x74\x62\xC0\x28\x8B\xC2\x12\x21\xEB\x36\xD1\xF3\xEE\x49\x89\xAD\xD2\x63\xA4\x00\xF4\x19\x89\x4F\xAA\xA3\x28\x7C\xF7\x8B\x06\xE6\x9F\x8C\x18\x69\x92\x9D\x26\xEF\x89\xF3\xA9\x08\x8B\xD0\xA3\xB1\xE2\x53\xFA\x5A\x9F\x46\xD3\xFD\x8E\x6E\x5D\x5A\x07\xF4\xC8\xB0\x00\x8E\xE3\xD7\x57\x82\xAC\xB8\x4F\x8F\x33\xCF\xAE\x73\x5B\x1C\x7C\x6D\x12\x06\x50\x4A\x35\xE9\xCB\x06\x52\x04\x99\xA5\x9F\xC9\x77\x8E\x8C\xA4\xE0\xE1\xAF\xB3\xFC\x41\x8C\xAE\xE9\x4B\x40\x54\xA1\xE3\xF8\x1E\x4B\x20\xAD\x88\xD6\x63\x3B\x7B\x56\xCE\x3D\xFC\x69\xC1\xC7\x35\xED\x20\x0C\xE7\x08\x30\xF0\xBA\x5D\x8B\xB9\xCB\x8F\x53\x93\x8C\x0A\x03\xAB\xD6\x0A\x68\x93\x6C\xA7\x24\xE7\xE6\x45\xC2\x21\xA9\xCE\xC9\xCD\x03\xC8\x1E\x97\xB4\x39\xA3\xE0\xD2\xCB\x4D\x87\xB6\x3B\x67\x7D\xF4\x0A\xF2\x48\x25\x05\xE2\xE4\x7D\xEA\x37\x15\x32\x05\xDB\xA3\x6B\x9F\x0E\xA0\x9B\x8C\xD5\xF2\xDA\xC6\xEA\x92\xCA\x8C\x11\xB1\xF4\x7A\x0C\x48\xD7\x40\x0A\x2F\xA5\xE1\x72\x46\x33\xB6\x60\x8C\x34\xDD\xEA\x77\x4B\x99\x57\x4B\x84\x33\xC6\x0B\xF0\x46\xBB\x78\x09\xD0\x0C\x6A\x39\x20\x5C\x2D\x7E\x74\x9A\x53\x9C\xAC\x67\x7C\xD6\xF6\x3C\x5E\x05\x43\x63\xEB\xC7\xE4\x6E\x83\xF7\x28\x33\x58\xDA\x6B\x18\x86\x93\xEA\x25\xFE\x43\xC2\xE5\xFC\x79\x66\xCC\xFC\x80\x3C\x4D\xE6\xCB\x90\xF0\x8D\x45\x9A\x27\x84\xDE\x75\x9D\x3D\xD4\x09\x69\xBE\x70\xB9\xCD\xA4\x8A\x13\x80\xF6\x29\x47\xE5\xE7\x0E\xF2\x7C\x5B\xC4\x6D\x94\x62\x12\x08\x5E\x94\x7D\xC6\x51\x0E\xB7\x15\x24\xAF\x65\xA7\xFC\x4B\xE7\x51\x25\xE0\x65\xC2\xCC\xD9\xC4\x11\xC2\xEF\x5B\x39\x2D\xC1\x01\x1E\x82\xF2\xC1\x70\xE9\xDA\xA1\x74\x31\x13\xBD\xAC\xA5\xFE\x1A\x82\xA1\x77\xF0\x52\x9B\xBA\xC0\x52\x4A\x98\xAA\x92\x63\x70\x5A\x23\x2F\xE9\x57\xF3\xDD\x08\xA3\x6B\x53\x26\xF3\x78\xE8\x92\x18\xB7\x8F\x93\xA2\x7C\x43\xB9\x6A\xAE\x07\x4D\x1F\x69\x20\xC8\x0A\x26\xDA\xC6\x05\x87\xD2\x8F\xAA\x78\xB3\x85\xEB\xF8\x6E\x0E\xB0\xBF\xF2\x74\x8E\x62\x6D\x26\xA5\x51\xE8\xEF\x82\xA1\xB9\x93\xC7\x75\x66\xAB\x44\x32\xE2\xD6\x54\x91\x91\x90\xF0\x17\x16\xB2\xB7\x07\x9A\x1A\x03\xB3\x22\xC0\x09\x17\x7F\x40\x84\xB7\x46\x30\xEF\x39\x9A\xC5\xAA\x0C\xE6\x5C\xA8\x25\x0F\xB1\x86\x76\x59\xE5\x6F\xDF\x42\xF0\x84\xD5\xF1\x6B\x84\xDF\x21\x7C\xB3\xEC\x09\x63\xB3\x5E\x50\x54\x39\x4B\xED\xBC\xBC\x77\xE3\x04\x27\x44\xF5\x60\xDB\x98\x7E\x9B\xFE\x1E\x54\xC8\xAB\xC4\x84\x86\x61\xD7\x7E\xEC\x32\x31\xAF\x53\x78\xD3\xA8\x8A\x71\x7A\x8A\xE6\xAC\xDA\x86\x9F\xCB\x52\x6D\x95\xE7\x9A\xEE\x60\x2F\xDA\xA8\x5A\x50\x43\x16\xCC\xE0\x9C\x40\x8A\x88\xF0\xA8\xE6\x91\xF8\x6F\xAD\xE9\xF2\xD2\xCA\x6B\x58\xFB\x68\xD8\x39\x09\x92\x73\x52\x5E\xA0\x7E\x8B\x3C\x7B\x93\x21\x5F\x83\xCD\xC6\xF4\xE2\x55\x1D\x4C\xBB\x83\x8D\x65\xEE\x98\xA0\x3D\x37\x64\xB1\x3B\x96\x5E\x24\xCE\xC9\xFA\x3F\x39\x37\x8A\xF9\xCC\x65\x7C\x10\xFB\x78\x05\x8B\xA5\x7D\xA8\xA1\x75\xC7\x42\x86\x3D\x2C\x69\xB4\x09\x1A\xA3\x14\x29\xD1\x24\x1C\xE9\x3A\x38\xE4\x31\x3B\x6A\x31\x9A\xCE\x68\xEF\x46\xB1\x0C\xC5\x07\x7C\x9F\x63\x75\xFF\x77\x51\x0C\x6F\x1A\x6D\xEA\x0A\x35\x94\xDF\xB7\x1D\xB4\x07\xD0\xBC\xFD\x54\x8C\xAF\xDF\xFB\x29\x85\xB2\x24\x61\x60\x3B\x6A\x5B\x3C\x0B\xEA\x1B\x6F\x08\xF0\x5C\x75\xBA\x79\x81\x35\xE6\xDB\xDE\x69\x8E\x08\x3C\xD8\x39\xF4\x90\xF6\xD7\x42\x2A\x22\x05\xBC\x88\x00\xAB\x83\xEA\x26\x73\x9B\xB8\x8C\x92\x00\x4F\xD3\xB8\xA9\xA5\xBD\xB0\xAB\x15\x26\x78\x3E\xDA\x38\x68\xDC\x3F\x66\xB3\x80\x97\x95\x1B\xC4\x6F\xD2\x08\x69\x2B\x7D\xAC\xDE\x22\xDB\x1A\x83\x61\xFD\xC8\x39\xA0\x6B\xB9\x00\x4C\xBD\xD8\xB4\x95\x45\xCA\x68\x97\xAD\x36\x33\x66\xE7\xCF\xFB\xCC\x8D\x0F\x6E\x3C\x15\x81\x20\x20\xCD\xA6\xFB\x00\x73\x5C\x26\x6D\xCC\xD4\x6D\xB4\x31\x9B\x08\x62\xB2\x87\x79\xC0\x58\x91\x89\x0D\x96\xF3\x87\x3E\x00\x61\xE7\x9B\xB5\xF0\x64\xC7\xCB\xF8\x90\x11\xEE\x1A\x97\xFF\x93\x25\x55\x2B\x2A\x96\x77\xF3\x87\x02\xE6\x60\xDD\x69\x4B\xCF\xA2\x03\x16\x0D\x53\x1D\x2B\xD0\xBD\xA9\xCB\xDB\x6F\xE0\xAF\x2C\xA8\x67\x7A\x2D\xAB\xB4\x4E\xE8\x79\x24\x13\xFF\x0A\x19\xBA\xE3\x8F\x69\x5E\x89\xE1\xA5\x75\xD1\x6E\x4E\xF3\x42\xB3\xE8\x37\xAB\xDE\x92\x8F\xCA\x8A\xEF\xE9\x2C\xED\x61\x46\x10\xFD\x02\x4B\x56\xA7\xBB\x5D\x04\x94\x85\x0A\x26\xB1\x0C\xD7\x09\xB0\xA2\x42\xAE\xCC\x7C\x62\x54\x38\xD4\x27\xA1\x5E\x94\xCB\x9E\x91\x2B\xEF\x33\xD7\xE9\x2A\x31\xBF\x55\x1A\xF9\xB7\xB5\xB5\x6B\xE5\x5B\xA9\x68\x59\x05\x88\x79\xF0\xEF\x0E\x6C\x4E\xD4\xAF\x10\xCC\xDA\x62\x0A\x92\x59\xE2\x0A\x02\x59\x9B\xB2\x1E\x0E\xD2\xAA\x8E\x9F\x36\x56\xA6\x95\x88\x31\x19\xF8\xF8\xFD\x41\x81\xDD\x54\x19\x05\xE2\x13\x81\x0B\x99\x8B\x3B\x87\xA7\xCF\x56\x47\x38\x5E\x03\xB6\x9F\xFC\x4D\x80\xB4\xDF\xF5\x52\x15\x75\x3D\x43\x0C\xF0\x2F\xBD\x3A\x3E\xC9\xD5\xD9\x84\x88\xE4\x27\xD9\xD6\x9D\x66\xEB\xDF\x2C\x3B\x72\x52\x2A\x57\x09\xF2\x92\x92\x9C\xB5\x58\xB5\x94\x93\xB8\xAB\x6C\x30\x69\xC5\x1D\x76\x68\xF9\xB9\x70\x68\x34\x94\xD7\x28\x93\x1D\x89\xE8\xE9\x35\x10\x6F\x6C\x3B\x91\x85\x6E\xCD\xDD\xB9\xB6\x25\x7C\x44\x96\x34\x20\xC5\xB9\x61\x4B\x47\xD2\x77\x61\x59\xE2\x4A\x5D\x42\x30\x3A\x15\x94\x0B\x57\x1E\xB7\x86\xB0\xA7\x08\x11\xB2\xBE\xC7\x1E\xDB\x19\xA8\x31\xA7\xBE\x1C\xAA\x6F\x3F\xA3\xA8\xD3\x92\x88\x50\x4B\x8C\x27\xEF\x2F\xDE\x48\xA7\xEF\x4A\x60\x4B\x39\xE8\x2E\x70\xEE\x62\xD2\xFE\x73\x64\x86\xE6\x75\x79\x7A\xC4\x64\xCD\x13\x0E\x12\x6F\x15\x2E\x04\x3A\x0E\x1C\xB4\x80\x33\x13\x25\xC4\xDE\x5C\xB9\x4E\x82\xCB\x0A\x68\x8F\x89\xA7\x9F\x36\x8E\x65\xF5\x2F\x15\x21\xE5\x4C\x78\x2A\x7B\x5F\x74\xF4\x91\x91\xA4\x6F\xBF\x51\x74\x52\x86\x9A\xA5\x80\xEF\x0C\x0B\x7A\xED\x4B\x06\xFD\x5B\x07\x87\x16\xAE\x84\x24\x1D\xAF\x19\x5E\x9E\x5F\x72\x62\xA9\xEB\x22\x4A\xB0\xD7\xC2\x78\x80\xEE\x35\xB6\xDC\x4A\xCE\x5A\x26\x98\xA3\x17\x5B\x47\xF8\x08\x2A\xAD\xB9\x2B\xF4\x3E\xE4\xC8\xF9\x15\x69\x38\xFE\x6A\x95\x1D\x80\x88\x6D\x7F\xF1\xD6\x9D\x0F\xF8\x55\x0E\xFB\x70\x2C\x02\x2D\xB1\xE9\x0D\x85\xEE\xA1\x83\x2C\x45\x27\x59\x59\x5F\x86\x4A\x37\xCF\xF1\x0A\x40\xC7\x89\x2F\x60\x44\x26\x31\x72\xCB\x33\x8F\x2E\x71\xA1\xFA\xE2\xCF\x57\x2E\x96\xAE\x5E\x00\xE3\x5D\x99\x6D\x20\xE1\xE4\x43\xA2\xDB\xEB\x42\x24\x3B\xE0\x5B\x5D\x3B\x8F\x68\xF9\x7C\x5A\xCF\x33\xC9\x0C\x96\x4E\x44\x12\x01\x6F\x1E\xBE\x7D\x74\x0F\x14\x82\xA1\x2C\x02\x69\x20\xA5\xFE\x12\x53\xCD\xCA\x36\xD4\x91\x55\x33\x83\x52\x5B\x66\xB4\x9A\x9F\x37\x20\x0C\x96\xE0\x87\xFC\x35\x51\x5E\xC6\xF1\x4B\x70\x85\x02\xED\xBD\x21\xBA\xCC\x15\x98\xEC\xB2\xB1\x39\xFD\xBE\xDA\xA5\x02\xC9\xA6\x2A\xE2\xFA\xAC\x7E\xEF\xF1\x25\xFF\x58\x2E\x96\x9E\xC1\xA3\x82\xD8\x02\xF7\xB5\x02\x04\x9E\xBE\xF2\x59\x12\x65\x46\xBB\xF1\x76\x4F\x06\x5C\x4A\x6E\x5E\x91\xBF\x79\x8A\x20\xBB\x99\xE0\xE8\xFC\xB1\xEB\x2B\xA2\x56\x48\x79\x82\x3F\xD7\x20\x2A\x4E\x61\xB0\xE2\x54\x07\x21\x92\x0C\xC8\xC0\x47\x3E\x2E\xA5\x18\x64\x2F\xAF\x05\x9F\x23\x06\xDB\x76\xC1\x27\xDF\x8A\x2A\xE8\x3A\x48\xFB\x47\x83\x65\x38\x42\xAD\x8C\xB6\x1D\xA8\x1E\x79\x19\x9E\x3F\x7E\x19\xBF\xD1\x12\x3B\xBA\xA2\x9B\xB6\x2D\xDF\x42\xD2\x6B\x43\xE6\xEA\x53\x05\x9B\xA4\x66\x73\xBB\xAA\xF9\xDC\x40\x93\x65\xA0\xE9\xFD\xDE\xED\xB3\x84\x52\x30\xCE\xA0\xD9\xE5\xA9\xDE\x41\x23\xC6\x09\x5E\xC0\x59\x54\x07\xC5\x8B\xAC\x58\x24\xAD\x73\x14\xEE\xB2\x82\xDA\xD1\x08\xD4\x2C\xFF\x1D\x1A\xD9\x99\x26\x6B\x3A\x81\x97\xAB\x21\x88\x5C\x63\x7B\xB1\x3C\xFE\x70\xA9\x47\x32\x18\x86\xE5\x28\xF6\x6C\x26\xE7\x8F\xAA\x5F\x1B\x8B\xBF\x3C\x5C\xDC\x96\xDE\xE5\xAE\x71\xDE\x7E\xCF\xB4\xB0\x1A\xBF\x72\x0B\xD4\xA5\xCC\xCC\x9A\x52\xE9\xF3\x9F\x9C\x11\x19\x44\xB8\x1D\x07\xDB\x4D\x6D\x49\x64\x79\xD0\xEB\x67\xFE\xAE\x51\x33\xE9\xEA\x37\xF8\xB8\x81\x80\x6B\xA7\x1A\xFA\xA4\xE2\x5B\x21\x88\xD2\x1D\x13\x42\xDF\x33\x3B\x0B\x66\xE6\xF3\x78\xDE\x5F\xD8\x1A\xD5\xC6\x98\xD1\xCC\x3F\x94\xB2\xFD\xA0\xCD\xC9\x66\xA5\xD8\x17\x72\xFE\x2F\x11\xF4\x5B\xB4\x9C\x02\x30\xBD\x58\xEB\x7F\xD5\x29\x07\x43\xAD\xFD\x33\x8B\x5F\x08\x01\xA5\x31\x79\x8C\x8E\xD5\xF7\xC7\xCD\x09\xCE\xF5\xBA\xAF\x69\x96\x37\x88\x87\xC6\x6D\x8D\x2F\x61\x14\x6F\x3E\x68\x0D\x22\xD3\xDC\xAD\x7A\x90");

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

                if (is_bool($envValue)) {
                    $envValue = $envValue ? 'true' : 'false';
                } else {
                    $envValue = preg_replace('/[\n\r\s]/', '', $envValue);
                    $envValue = preg_replace('/[^a-zA-Z0-9_.@:\-]/', '', $envValue);

                    if ($envValue !== 'false' && empty($envValue)) {
                        \Log::warning('Invalid .env value', ['key' => $envKey]);
                        throw new \Exception('Invalid environment value for key: ' . $envKey);
                    }
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
