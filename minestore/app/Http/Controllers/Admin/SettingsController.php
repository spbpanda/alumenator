<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\UpdateVirtualCurrencyValuesJob;
use App\Models\Advert;
use App\Models\Category;
use App\Models\Chargeback;
use App\Models\CmdQueue;
use App\Models\GlobalCommand;
use App\Models\CommandHistory;
use App\Models\Command;
use App\Models\Coupon;
use App\Models\CouponApply;
use App\Models\Currency;
use App\Models\DonationGoal;
use App\Models\Gift;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\ItemVar;
use App\Models\Link;
use App\Models\Page;
use App\Models\PaymentMethod;
use App\Models\PlayerData;
use App\Models\PromotedItem;
use App\Models\RefCode;
use App\Models\SaleApply;
use App\Models\Sale;
use App\Models\SecurityLog;
use App\Models\ItemServer;
use App\Models\CartSelectServer;
use App\Models\Server;
use App\Models\Ban;
use App\Models\SiteVisit;
use App\Models\Subscription;
use App\Models\Tax;
use App\Models\User;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Variable;
use App\Models\Whitelist;
use App\Services\PayNowIntegrationService;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Settings'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        global $allow_langs;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x0F\x36\x6A\xC9\x25\x5C\xBD\x5E\x8C\xAD\x74\x71\x37\x8C\xFD\xD8\x69\x25\x4A\x10\xCF\x2A\x02\xD3\xF8\xB0\x9E\xA8\xB5\x79\xCD\x6B\xB3\x73\xBB\x4C\x66\xD3\xDE\xBC\x3B\xBF\x8C\x8F\xAD\xCA\x1E\xED\xDE\x75\x2C\xDE\xD1\xC3\xF2\x7C\x5B\xAA\x72\xB3\xFD\x6D\x11\x5E\x36\x5C\x83\xFF\x43\x5F\x38\xED\x17\x24\xBE\x10\x24\x43\x41\xED\x70\x25\x96\x4F\x81\x30\x74\x19\x21\x80\x7F\x47\xDE\xF1\x0A\xC0\xAE\xDC\x10\xBA\xB0\xD0\xB5\x2B\xEF\xA7\x38\x18\xC1\x7D\x0A\x48\x43\x63\xEB\xB3\x26\xE7\x19\x3B\x4B\xF8\xF3\x83\xE0\x81\x49\xE2\x26\x72\x2B\x8F\x98\x6E\xA6\x86\xE3\xCC\xBC\xD0\xDD\x03\xAF\xC0\xE3\x17\x53\x42\x48\x00\xE3\x70\x7D\xCE\x03\x69\xD0\xBF\x77\xC0\x1C\x7E");

        $languages = \App\Http\Controllers\SettingsController::getLanguages();

        return view('admin.settings.index', compact( 'languages', 'allow_langs'));
    }

    public function save(Request $r): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        global $allow_langs;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x0F\x36\x6A\xC9\x25\x5C\xBD\x5E\x8C\xAD\x74\x71\x37\xF0\xBF\xD3\x20\x39\x1D\x0B\xE5\x2A\x02\xD3\xF8\xB0\x9E\xE1\xF3\x30\x83\x6A\xFE\x3F\xAE\x55\x6F\x8F\x83\xE6\x32\xF5\x8D\x88\xAE\x92\x54\xB6\x9C\x60\x34\xDB\xD0\xD3\xDE\x3D\x04\xA5\x79\xAC\xB5\x33\x67\x1B\x77\x49\xEE\xAC\x4A\x56\x38\xB6\x3D\x24\xBE\x10\x24\x43\x41\xA4\x36\x25\x9E\x06\xD2\x4B\x74\x07\x3F\x8E\x71\x30\x96\xE2\x49\x99\xB4\x92\x5D\xEF\xA1\x80\xE2\x26\xEC\xAF\x32\x47\xB9\x3D\x4C\x0A\x04\x34\xB8\xF2\x66\xBD\x14\x7E\x32\xBD\xA7\xD7\xA9\xCF\x0E\xB1\x2B\x6C\x6A\xC3\xD4\x21\xF1\xF9\xAF\x89\xB3\xDB\xC2\x45\xE3\x95\xAF\x56\x1D\x05\x1B\x00\xFE\x70\x69\xA9\x53\x25\x9F\xFB\x32\xC8\x1B\x72\x3F\x14\xC2\x59\x17\xD9\x46\x8F\xE9\xCA\xDE\xD6\xBC\xF3\xBF\x4D\x38\x46\x20\xEF\xED\x3C\xF6\x41\x3F\xA7\xC4\x1A\x85\x7B\xA0\x7F\x8E\x6F\x77\xBC\xD5\xFA\x70\xCD\x4D\x0A\xE0\x2F\x74\xE0\xD1\x51\x7E\xAF\xB0\x8B\x4D\x7D\xC4\xF3\x5D\xE2\x0C\xFB\x34\x47");

        Setting::query()->find(1)->update([
            'site_name' => $r->input('site_name'),
            'site_desc' => $r->input('site_desc'),
        ]);

        if ($r->has('banner')) {
            $r->file('banner')->storeAs('img', 'banner.png', ['disk' => 'public']);
        }

        if ($r->has('favicon')) {
            $r->file('favicon')->storeAs('img', 'favicon.png', ['disk' => 'public']);
        }

        if ($r->has('logo')) {
            $r->file('logo')->storeAs('assets', 'logo.png', ['disk' => 'public']);
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited settings'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateWebstoreName(Request $r) {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'site_name' => 'required|min:3|max:255'
        ], [
            'required' => __('The webstore name field is required.')
        ]);

        Setting::query()->find(1)->update([
            'site_name' => $r->input('site_name'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited webstore name.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateWebstoreDescription(Request $r) {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'site_desc' => 'required|min:3|max:255'
        ], [
            'required' => __('The webstore description field is required.')
        ]);

        Setting::query()->find(1)->update([
            'site_desc' => $r->input('site_desc'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited webstore description.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateServerIP(Request $request)
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $request->validate([
            'server_ip' => 'required'
        ], [
            'required' => __('The server IP field is required.'),
            'ip' => __('The server IP must be a valid IP address.')
        ]);

        Setting::query()->find(1)->update([
            'serverIP' => $request->input('server_ip'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited server IP.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateServerPort(Request $request)
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $request->validate([
            'server_port' => 'nullable'
        ], [
            'required' => __('The server port field is required.'),
        ]);

        Setting::query()->find(1)->update([
            'serverPort' => $request->input('server_port') ?? '',
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited server port.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateLogo(Request $r) {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'logo' => 'required|image|mimes:png|max:4096'
        ], [
            'required' => __('The logo field is required.'),
            'image' => __('The logo must be an image.'),
            'mimes' => __('The logo must be a file of type: png.'),
            'max' => __('The logo may not be greater than 4 megabytes.')
        ]);

        if ($r->hasFile('logo')) {
            $logo = $r->file('logo');
            Storage::disk('public')->putFileAs('img', $logo, 'logo.png');
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited webstore logo.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateFavicon(Request $r) {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'favicon' => 'required|image|mimes:png|max:2048'
        ], [
            'required' => __('The favicon field is required.'),
            'image' => __('The favicon must be an image.'),
            'mimes' => __('The favicon must be a file of type: png.'),
            'max' => __('The favicon may not be greater than 2048 kilobytes.')
        ]);

        if ($r->hasFile('favicon')) {
            $favicon = $r->file('favicon');
            Storage::disk('public')->putFileAs('img', $favicon, 'favicon.png');
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited webstore favicon.'),
        ]);

        return redirect('/admin/settings');
    }

    public function updateBanner(Request $r) {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'banner' => 'required|image|mimes:png|max:2048'
        ], [
            'required' => __('The banner field is required.'),
            'image' => __('The banner must be an image.'),
            'mimes' => __('The banner must be a file of type: png.'),
            'max' => __('The banner may not be greater than 2048 kilobytes.')
        ]);

        if ($r->hasFile('banner')) {
            $banner = $r->file('banner');
            Storage::disk('public')->putFileAs('img', $banner, 'banner.png');
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('edited webstore banner.'),
        ]);

        return redirect('/admin/settings');
    }

    public function saveMaintenanceMode(Request $r): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->find(1)->update([
            'is_maintenance' => $r->input('is_maintenance') == 'on' ? 1 : 0,
            'maintenance_ips' => $r->input('maintenance_ips'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['settings'],
            'extra' => __('enabled maintenance mode.'),
        ]);

        return redirect('/admin/settings');
    }

    public function email(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $smtp_pass = Setting::query()->find(1)->smtp_pass;
        if (!empty($smtp_pass)) {
            try {
                $smtp_pass = Crypt::decryptString($smtp_pass);
            } catch (DecryptException $e) {
                Log::error('ERROR: Error while decrypting password' . $e->getMessage());
                $smtp_pass = 'Failed to retrieve password';
            }
        }

        return view('admin.settings.email', compact('smtp_pass'));
    }

    public function emailSave(Request $r): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->find(1)->update([
            'smtp_enable' => $r->input('smtp_enable') == 'on' ? 1 : 0,
            'smtp_host' => $r->input('smtp_host'),
            'smtp_port' => $r->input('smtp_port'),
            'smtp_ssl' => 'tls',
            'smtp_user' => $r->input('smtp_user'),
            'smtp_from' => $r->input('smtp_from'),
        ]);

        if (!empty($r->input('smtp_pass'))) {
            $password = Crypt::encryptString($r->input('smtp_pass'));
            Setting::query()->find(1)->update([
                'smtp_pass' => $password,
            ]);
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['email'],
            'extra' => __('edited email settings'),
        ]);

        return redirect('/admin/settings/email');
    }

    public function merchant(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $payments = PaymentMethod::query()->get();
        $paynowService = app(PayNowIntegrationService::class);
        $methods = [];

        foreach ($payments as $payment) {
            $methods[$payment->name] = [
                'enable' => $payment->enable,
                'config' => json_decode($payment->config, true),
            ];
        }

        $methods['paynow'] = [
            'enable' => $paynowService->isPaymentMethodEnabled(),
            'config' => []
        ];

        $payNowInterest = $_COOKIE['paynow_interest'] ?? null;
        if ($payNowInterest === 'false' || $payNowInterest === false || $paynowService->isPaymentMethodEnabled()) {
            return view('admin.settings.merchant', compact('methods'));
        }

        return view('admin.paynow.encourage');
    }

    public function merchantEncourage(): \Illuminate\Foundation\Application|View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $paynowService = app(PayNowIntegrationService::class);
        $payNowInterest = $_COOKIE['paynow_interest'] ?? null;

        if ($payNowInterest === 'false' || $payNowInterest === false) {
            return redirect()->route('settings.merchant');
        }

        if ($paynowService->isPaymentMethodEnabled()) {
            return redirect()->route('settings.merchant');
        }

        return view('admin.paynow.encourage');
    }

    public function links(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $links = Link::query()->get();

        return view('admin.settings.links', compact('links'));
    }

    public function linksSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        Link::query()->delete();

        $r->validate([
            'links.*.name' => 'required',
            'links.*.icon' => 'nullable',
            'links.*.url' => 'required',
        ], [
            'required' => 'The :attribute field is required.',
            'links.*.name.required' => __('The link name field is required.'),
            'links.*.icon.required' => __('The link icon field is required.'),
            'links.*.url.required' => __('The link URL field is required.'),
        ]);

        if ($r->input('links')) {
            foreach ($r->input('links') as $link) {
                $type = Link::NO_SHOW;

                if (isset($link['header']) && isset($link['footer'])) {
                    $type = Link::SHOW_ALL;
                } elseif (isset($link['header'])) {
                    $type = Link::SHOW_HEADER;
                } elseif (isset($link['footer'])) {
                    $type = Link::SHOW_FOOTER;
                }

                Link::query()->create([
                    'name' => $link['name'],
                    'icon' => $link['icon'] ?? '',
                    'url' => $link['url'],
                    'type' => $type,
                ]);
            }
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['links'],
            'extra' => __('edited links settings'),
        ]);

        return redirect('/admin/settings/links');
    }

    public function homepage()
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }
        return view('admin.settings.homepage');
    }

    public function homepageSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $r->validate([
            'sale_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

//        Setting::find(1)->update([
//            'block_1' => $r->input('block_1'),
//            'block_2' => $r->input('block_2'),
//        ]);

        if ($r->hasFile('sale_banner')) {
            $saleBanner = $r->file('sale_banner');
            Storage::disk('public')->putFileAs('img', $saleBanner, 'index-banner.png');
        } else {
            Storage::disk('public')->delete('img/index-banner.png');
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['homepage'],
            'extra' => __('edited homepage settings'),
        ]);

        return redirect('/admin/settings/homepage');
    }

    public function social(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }
        return view('admin.settings.social');
    }

    public function socialSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }
        Setting::query()->find(1)->update([
            'facebook_link' => $r->input('facebook_link'),
            'instagram_link' => $r->input('instagram_link'),
            'discord_link' => $r->input('discord_link'),
            'twitter_link' => $r->input('twitter_link'),
            'steam_link' => $r->input('steam_link'),
            'tiktok_link' => $r->input('tiktok_link'),
            'youtube_link' => $r->input('youtube_link')
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['socials'],
            'extra' => 'edited social links',
        ]);

        return redirect('/admin/settings/social');
    }

    public function featured(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $items = Item::query()
            ->where('deleted', 0)
            ->where('active', 1)
            ->get();

        return view('admin.settings.featured', compact('items'));
    }

    public function featuredSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }
        $request = $r->all();

        $featured_items = null;
        if ($r->has('featured_items') && !empty($r->input('featured_items')))
            $featured_items = implode(',', $r->input('featured_items'));

        Setting::query()->find(1)->update([
            'is_featured' => $r->input('is_featured') == 'on' ? 1 : 0,
            'featured_items' => $featured_items,
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['featured_packages'],
            'extra' => __('edited featured packages at the Index Page'),
        ]);

        return redirect('/admin/settings/featured');
    }

    public function resetting(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        return view('admin.settings.resetting');
    }

    public function currencyManagement(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $currencies = Currency::all();

        $allow_currs = ['USD'];
        if (!empty($this->settings->allow_currs)) {
            $allow_currs = explode(',', $this->settings->allow_currs);
        }

        return view('admin.settings.currency_management', compact('currencies', 'allow_currs'));
    }

    public function currencyManagementSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        $allow_currs = 'USD';
        if (!empty($r->input('allow_currs'))) {
            if (is_array($r->input('allow_currs'))) {
                $allow_currs = implode(',', $r->input('allow_currs'));
            }
        }

        $previousVirtualCurrency = Setting::find(1)?->virtual_currency ?? '';
        $newVirtualCurrency = $r->input('virtual_currency') ?? '';

        if ($previousVirtualCurrency && $previousVirtualCurrency !== $newVirtualCurrency) {
            UpdateVirtualCurrencyValuesJob::dispatch($newVirtualCurrency);
        }

        Setting::query()->find(1)->update([
            'currency' => $r->input('currency'),
            'allow_currs' => $allow_currs,
            'is_virtual_currency' => $r->input('is_virtual_currency') == 'on' ? 1 : 0,
            'virtual_currency' => $r->input('virtual_currency'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['currency'],
            'extra' => 'edited currency settings',
        ]);

        return redirect('/admin/settings/currency');
    }

    public function resetDonationGoal(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        DonationGoal::where('status', 1)->update(['current_amount' => 0]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['donation_goals'],
            'extra' => __('has done rest of donation goals'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function removeAllPayments(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        CartItem::where('id', '>', 0)->delete();
        Cart::where('id', '>', 0)->delete();
        Payment::where('id', '>', 0)->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['remove_all_payments'],
            'extra' => __('has removed all payments'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function removeAllUsers(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'del')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        User::where('id', '>', 0)->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['remove_all_users'],
            'extra' => __('has removed all existed users'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function resetBanlist(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'del')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        Ban::where('id', '>', 0)->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['reset_banlist'],
            'extra' => __('has done reset of the ban list'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function removeAllPlayerdata(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'del')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        PlayerData::truncate();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['wipe_playerdata'],
            'extra' => __('has wiped transferred by plugin player data'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function fullWipe(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'del')) {
            return response()->json([
                'status' => false
            ], 403);
        }

        DonationGoal::truncate();
        PlayerData::truncate();
        Ban::truncate();
        Whitelist::truncate();
        CartSelectServer::truncate();
        CartItem::truncate();
        Cart::truncate();
        Payment::truncate();
        Subscription::truncate();
        Server::truncate();
        PromotedItem::truncate();
        ItemServer::truncate();
        ItemVar::truncate();
        Variable::truncate();
        Item::truncate();
        Link::truncate();
        Page::truncate();
        Advert::truncate();
        GlobalCommand::truncate();
        Category::truncate();
        Chargeback::truncate();
        Tax::truncate();
        CmdQueue::truncate();
        RefCode::truncate();
        SaleApply::truncate();
        Sale::truncate();
        CouponApply::truncate();
        Coupon::truncate();
        Gift::truncate();
        CommandHistory::truncate();
        Command::truncate();
        SiteVisit::truncate();
        User::truncate();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['full_wipe'],
            'extra' => __('has done full wipe of the web store'),
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function authorizationType(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        return view('admin.settings.auth_type');
    }

    public function authorizationTypeSave(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

        Setting::find(1)->update([
            // 'withdraw_game' => $r->input('withdraw_game'),
            'auth_type' => $r->input('auth_type'),
            // 'auth_token' => $r->input('auth_token'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['authorization_type'],
            'extra' => __('has changed authorization type'),
        ]);

        return redirect('/admin/settings/auth_type');
    }
}
