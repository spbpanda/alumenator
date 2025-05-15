<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\PaymentMethod;
use App\Models\SecurityLog;
use App\Models\Setting;
use App\Models\Theme;
use DateTimeZone;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PDO;
use PDOException;
use Validator;

class InstallController extends Controller
{
    public function index()
    {
        if (config('app.is_installed'))
            return redirect('/');

        $pageTitle = 'MineStoreCMS - Installation';

        $currencies = [];
        $currenciesJSON = @file_get_contents('https://minestorecms.com/api/currency/' . config('app.LICENSE_KEY'), false, stream_context_create(['http' => ['timeout' => 8]]));
        if ($currenciesJSON)
            $currencies = json_decode($currenciesJSON, false);

        $timezones = DateTimeZone::listIdentifiers();
        $languages = SettingsController::getLanguages(false);

        $versionUse = true;
        $isFPM = stripos(strtolower(php_sapi_name()), "fpm") !== false;
        $isTimezone = function_exists("zval_zone");
        $licenseKey = config("app.LICENSE_KEY");
        if (version_compare(PHP_VERSION, '8.3.0', '<') || version_compare(PHP_VERSION, '8.4.0', '>'))
            $versionUse = false;

        $response = @file_get_contents('https://minestorecms.com/checker/' . $licenseKey, false, stream_context_create(['http' => ['timeout' => 10]]));
        $response = json_decode($response, true);

        $isLicenseValid = false;
        if ($response['status'] === 'success') {
            $currentDomain = $response['message'];
            $myDomain = request()->getHost();

            $isLicenseValid = ($currentDomain === $myDomain);
        }

        return view('install.index', compact('isFPM', 'isTimezone', 'versionUse', 'licenseKey', 'languages', 'currencies', 'timezones', 'isLicenseValid'))->with('pageTitle', $pageTitle);
    }

    public function diagnose()
    {
        if (config('app.is_installed')) {
            return response()->json(['status' => false, 'message' => 'Application is already installed'], 403);
        }

        $versionUse = true;
        $isFPM = stripos(strtolower(php_sapi_name()), "fpm") !== false;
        $isTimezone = function_exists("zval_zone");
        $licenseKey = config("app.LICENSE_KEY");
        if (version_compare(PHP_VERSION, '8.3.0', '<') || version_compare(PHP_VERSION, '8.4.0', '>'))
            $versionUse = false;

        $response = @file_get_contents('https://minestorecms.com/checker/' . $licenseKey, false, stream_context_create(['http' => ['timeout' => 10]]));
        $response = json_decode($response, true);

        $isLicenseValid = false;
        if ($response['status'] === 'success') {
            $currentDomain = $response['message'];
            $myDomain = request()->getHost();

            $isLicenseValid = ($currentDomain === $myDomain);
        }

        return view('install.diagnose', compact('isFPM', 'isTimezone', 'versionUse', 'licenseKey', 'isLicenseValid'));
    }

    public function changeLicenseKey(Request $r)
    {
        if (config('app.is_installed')) {
            return response()->json(['status' => false, 'message' => 'Application is already installed'], 403);
        }

        $request = $r->validate([
            'preInstall_licenseKey' => 'required|string',
        ]);

        SettingsController::setEnvironmentValue([
            'LICENSE_KEY' => $r->input('preInstall_licenseKey'),
        ]);
        Artisan::call('config:clear');

        return response()->json(['status' => true]);
    }

    /**
     * @throws ValidationException
     */
    public function install(Request $r)
    {
        if (config('app.is_installed')) {
            return redirect('/');
        }

        // Handle logo upload
        if ($r->hasFile('webstore_logo')) {
            Storage::disk('public')->putFileAs('img', $r->file('webstore_logo'), 'logo.png');
        }

        $rules = [
            'DB_HOST' => 'required|string',
            'DB_PORT' => 'required|numeric',
            'DB_DATABASE' => 'required|string',
            'DB_USERNAME' => 'required|string',
            'admin_username' => 'required|string',
            'admin_password' => 'required|string|confirmed',
            'admin_password_confirmation' => 'required|same:admin_password',
        ];

        $validator = Validator::make($r->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return response()->json([
                'status' => 'validationFail',
                'errors' => $errors,
            ], 422);
        }

        $validated = $r->all();

        $database_password = $r->post('DB_PASSWORD');

        // Attempt to connect to the database
        try {
            $host = $validated['DB_HOST'];
            $username = $validated['DB_USERNAME'];
            $database = $validated['DB_DATABASE'];
            $port = $validated['DB_PORT'] ?? 3306;

            // Use PDO to connect to the database
            $dsn = "mysql:host=$host;port=$port;dbname=$database";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $connection = new PDO($dsn, $username, $database_password, $options);
        } catch (PDOException $e) {
            return response()->json([
                'status' => 'dbFail',
                'message' => 'Database connection failed. ' . $e->getMessage(),
            ]);
        }

        // Save database configuration to .env file
        SettingsController::setEnvironmentValue([
            'DB_HOST' => $validated['DB_HOST'],
            'DB_PORT' => $validated['DB_PORT'] ?? 3306,
            'DB_DATABASE' => $validated['DB_DATABASE'],
            'DB_USERNAME' => $validated['DB_USERNAME'],
            'DB_PASSWORD' => '"' . $database_password . '"',
        ]);

        // Clear caches and generate app key
        Artisan::call('key:generate');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        // Migrate the database
        try {
            Artisan::call('migrate');
        } catch (Exception $e) {
            return response()->json([
                'status' => 'migrateFail',
                'message' => 'Database migration failed. ' . $e->getMessage(),
            ]);
        }

        // Generate API secret key
        $apiKey = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 20);

        // Build the allowed currencies list
        $allowed_currencies = 'USD,EUR,BRL,CAD,GBP,CZK,TRY,SEK,PLN';
        $allow_currs = $allowed_currencies . (!str_contains($allowed_currencies, $r->input('webstore_currency')) ? ',' . $r->input('webstore_currency') : '');

        // Build allowed languages list
        $allowed_languages = 'en,fr,nl,no,it,he,cs,da,de,es-ES,ua,ro,ru,sv-SE,br,pt-PT';
        $allow_langs = $allowed_languages . (!str_contains($allowed_languages, $r->input('webstore_language')) ? ',' . $r->input('webstore_language') : '');

        // Save settings to the database
        Setting::truncate();
        Setting::query()->create([
            // General Settings Step
            'site_name' => $r->input('webstore_name') ?? 'MineStore Webstore',
            'site_desc' => $r->input('webstore_description') ?? 'Welcome to our webstore!',
            'currency' => $r->input('webstore_currency') ?? 'USD',
            'lang' => $r->input('webstore_language') ?? 'en',
            // Monitoring Details
            'serverIP' => $r->input('webstore_serverIP') ?? 'mc.example.com',
            'serverPort' => $r->input('webstore_serverPort') ?? '25565',
            'webhook_url' => $r->input('webstore_discordWebhookURL') ?? '',
            'discord_guild_id' => $r->input('webstore_discordServerID') ?? '',
            'discord_url' => $r->input('webstore_discordInviteLink') ?? '',
            'share_metrics' => $r->input('webstore_shareMetrics') !== null ? 1 : 0,
            // Other Settings (set by default)
            'theme' => 1,
            'is_api' => 1,
            'withdraw_game' => 'minecraft',
            'auth_type' => 'username',
            'api_secret' => $apiKey,
            'allow_langs' => $allow_langs,
            'allow_currs' => $allow_currs,
            'block_1' => '<p>' . __('To begin shopping, please select a category from the sidebar. Please note that ranks cost a one-time fee and are unlocked permanently!') . '</p>',
            'block_2' => '<div style="color:#ffae00; font-size:20px; font-weight:700; text-transform:uppercase">
                    <h2>' . __('SUPPORT / QUESTIONS') . '</h2>
                    </div>

                    <div style="color:#ffae00; font-size:16px; line-height:normal; margin-top:5px">
                    <p>' . __('Need any questions answered before checkout? Waited more than 20 minutes but your package still has not arrived? Ask the community/staff on Discord, or for payment support, submit a support ticket on our website.') . '</p>
                    </div>

                    <div style="color:#ff3c00; font-size:20px; font-weight:700; margin-top:35px; text-transform:uppercase">
                    <h2>' . __('REFUND POLICY') . '</h2>
                    </div>

                    <div style="color:#ea6f05; font-size:16px; line-height:normal; margin-top:5px">
                    <p>' . __('All payments are final and non-refundable. Attempting a chargeback or opening a PayPal dispute will result in permanent and irreversible banishment from all of our servers, and other minecraft stores.') . '</p>
                    </div>

                    <div style="color:#ff3c00; font-size:16px; line-height:normal; margin-top:30px">
                    <p>' . __('It could take between 1-20 minutes for your purchase to be credited in-game. If you are still not credited after this time period, please open a support ticket on our forums with proof of purchase and we will look into your issue.') . '</p>
                    </div>',
            'index_deal' => 0,
        ]);

        Theme::truncate();

        // Fetching latest default theme version
        $context = stream_context_create(['http' => ['timeout' => 10]]);
        $themesUrl = 'https://minestorecms.com/cms/' . env('LICENSE_KEY') . '/themes?new_version=' . config('app.version');
        $themes = @file_get_contents($themesUrl, false, $context);

        if ($themes === false) {
            return response()->json([
                'status' => 'theme_fetch_failed',
                'message' => 'Unable to fetch themes from URL.',
            ]);
        }

        $themesArray = json_decode($themes, true);
        if (!is_array($themesArray)) {
            return response()->json([
                'status' => 'theme_fetch_failed',
                'message' => 'Themes fetched from URL are not an array.',
            ]);
        }

        $defaultThemeVersion = '3.1.2';
        $defaultTheme = collect($themesArray)->firstWhere('id', 1);

        if ($defaultTheme) {
            $defaultThemeVersion = $defaultTheme['version'];
        }

        Theme::create([
            'theme' => 1,
            'name' => 'Default Theme',
            'description' => 'Default Theme for 3.x Version',
            'img' => 'https://i.imgur.com/EtTN8yO.png',
            'url' => '',
            'author' => 'MineStoreCMS',
            'is_custom' => 0,
            'version' => $defaultThemeVersion,
        ]);

        Admin::truncate();
        $admin = Admin::query()->create([
            'username' => $validated['admin_username'],
            'password' => Hash::make($validated['admin_password']),
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'rules' => '{"isAdmin": true}',
        ]);

        PaymentMethod::truncate();
        Artisan::call('db:seed --class=PaymentMethodsSeeder');
        Artisan::call('db:seed --class=PayNowSeeder');

        SettingsController::setEnvironmentValue([
            'APP_DEBUG' => false,
            'INSTALLED' => true,
        ]);

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('currency:update');

        SecurityLog::create([
            'admin_id' => $admin->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['install'],
        ]);

        @file_get_contents('https://minestorecms.com/g/'.request()->getHost().'/1', false, stream_context_create(['http' => ['timeout' => 10]]));

        return response()->json([
            'status' => 'OK',
        ]);
    }
}
