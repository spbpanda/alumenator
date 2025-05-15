<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PagesController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

/*$lang = Cookie::get('lang');
if (empty($lang) && env('INSTALLED')) {
    $lang = Setting::query()->select('lang')->find(1)->lang;
}
App::setLocale($lang);*/

Route::GET('/api/checkAccessibility', [PagesController::class, 'checkAccessibility']);

Route::prefix('admin')->middleware('installed')->group(function () {
    Route::get('/login', [Admin\LoginController::class, 'index']);
    Route::post('/login', [Admin\LoginController::class, 'login']);
    Route::post('/verify2fa', [Admin\LoginController::class, 'verify2fa']);
    Route::any('/logout', [Admin\LoginController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [Admin\IndexController::class, 'index'])->name('index');
        Route::post('/upgrade', [Admin\IndexController::class, 'upgrade']);
		Route::get('/themeStyle', [Admin\IndexController::class, 'themeStyle']);
		Route::get('/search', [Admin\IndexController::class, 'search']);

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [Admin\CategoriesController::class, 'index'])->name('index');
            Route::get('/new', [Admin\CategoriesController::class, 'new'])->name('new');
            Route::post('/new', [Admin\CategoriesController::class, 'create']);
            Route::post('/updateSort', [Admin\CategoriesController::class, 'updateSort'])->name('updateSort');
            Route::post('/delete/{id}', [Admin\CategoriesController::class, 'delete'])->name('delete');
            Route::get('/comparisons/{id}', [Admin\CategoriesController::class, 'comparisons'])->name('comparisons');
            Route::get('/{id}', [Admin\CategoriesController::class, 'category'])->name('view');
            Route::post('/{id}', [Admin\CategoriesController::class, 'save']);
        });

        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [Admin\ItemsController::class, 'index'])->name('index');
            Route::get('/new', [Admin\ItemsController::class, 'new'])->name('new');
            Route::post('/delete/{id}', [Admin\ItemsController::class, 'delete'])->name('delete');
            Route::get('/duplicate/{id}', [Admin\ItemsController::class, 'duplicate'])->name('duplicate');
            Route::post('/update-sort', [Admin\ItemsController::class, 'updateSort'])->name('updateSort');
            Route::get('/{id}/{c_url}/comparison', [Admin\ItemsController::class, 'getComparison'])->name('comparison');
            Route::get('/{id}', [Admin\ItemsController::class, 'item'])->name('view');
            Route::post('/{id}', [Admin\ItemsController::class, 'save']);
        });

        Route::resource('coupons', Admin\CouponController::class)->except('show');
        Route::resource('gifts', Admin\GiftsController::class)->except('show');
        Route::resource('sales', Admin\SalesController::class)->except('show');
        Route::resource('vars', Admin\VarsController::class)->except('show');
        Route::resource('refs', Admin\RefsController::class)->except('edit');
        Route::resource('donation_goals', Admin\DonationGoalsController::class);

        Route::resource('bans', Admin\BansController::class)
            ->only(['index', 'create', 'store']);
        Route::resource('whitelist', Admin\WhitelistController::class)
            ->only(['index', 'create', 'store']);

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [Admin\PagesController::class, 'index'])->name('index');
            Route::get('/create', [Admin\PagesController::class, 'new'])->name('new');
            Route::post('/create', [Admin\PagesController::class, 'create'])->name('create');
            Route::post('/delete/{id}', [Admin\PagesController::class, 'delete'])->name('delete');
            Route::get('/staff', [Admin\PagesController::class, 'staff'])->name('staff');
            Route::get('/staff/create', [Admin\PagesController::class, 'staff_create'])->name('staff.create');
            Route::get('/staff/edit/{id}', [Admin\PagesController::class, 'staff_edit'])->name('staff.edit');
            Route::post('/staff/create', [Admin\PagesController::class, 'staff_store'])->name('staff.store');
            Route::get('/profile', [Admin\PagesController::class, 'profile'])->name('profile');
            Route::post('/profile', [Admin\PagesController::class, 'profile_save']);
            Route::get('/{id}', [Admin\PagesController::class, 'page'])->name('view');
            Route::post('/edit/{id}', [Admin\PagesController::class, 'edit'])->name('edit');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/markAsPaid/{id}', [Admin\PaymentsController::class, 'markAsPaid'])->name('markAsPaid');
            Route::post('/resend/{id}', [Admin\PaymentsController::class, 'resend'])->name('resend');
            Route::post('/delete/cmd/{id}', [Admin\PaymentsController::class, 'deleteCMD'])->name('cmd.delete');
            Route::post('/resend/all/{id}', [Admin\PaymentsController::class, 'resendAllCommands'])->name('cmd.resendAll');
        });

        Route::resource('payments', Admin\PaymentsController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::prefix('rcon')->name('rcon.')->group(function () {
            Route::get('/', [Admin\RconController::class, 'index'])->name('index');
            Route::post('/sendCommand', [Admin\RconController::class, 'sendCommand'])->name('sendCommand');
        });

        Route::resource('users',Admin\UsersController::class)->except('edit');

        Route::resource('advert', Admin\AdvertController::class)->only(['index', 'store']);

        Route::prefix('discord')->name('discord.')->group(function () {
            Route::get('/', [Admin\DiscordController::class, 'index'])->name('index');
            Route::post('/', [Admin\DiscordController::class, 'store'])->name('store');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [Admin\SettingsController::class, 'index'])->name('index');
            Route::post('/updateWebstoreName', [Admin\SettingsController::class, 'updateWebstoreName'])->name('updateWebstoreName');
            Route::post('/updateWebstoreDescription', [Admin\SettingsController::class, 'updateWebstoreDescription'])->name('updateWebstoreDescription');
            Route::post('/updateServerIP', [Admin\SettingsController::class, 'updateServerIP'])->name('updateServerIP');
            Route::post('/updateServerPort', [Admin\SettingsController::class, 'updateServerPort'])->name('updateServerPort');
            Route::post('/updateLogo', [Admin\SettingsController::class, 'updateLogo'])->name('updateLogo');
            Route::post('/updateFavicon', [Admin\SettingsController::class, 'updateFavicon'])->name('updateFavicon');
            Route::post('/updateBanner', [Admin\SettingsController::class, 'updateBanner'])->name('updateBanner');
            Route::post('/', [Admin\SettingsController::class, 'saveMaintenanceMode'])->name('maintenanceSave');
            Route::get('/email', [Admin\SettingsController::class, 'email'])->name('email');
            Route::post('/email', [Admin\SettingsController::class, 'emailSave'])->name('emailSave');
            Route::get('/merchant', [Admin\SettingsController::class, 'merchant'])->name('merchant');
            Route::get('/links', [Admin\SettingsController::class, 'links'])->name('links');
            Route::post('/links', [Admin\SettingsController::class, 'linksSave'])->name('linksSave');

            Route::resource('servers', Admin\Settings\ServerSettingsController::class)->only(['index', 'create']);

            Route::get('/homepage', [Admin\SettingsController::class, 'homepage'])->name('homepage');
            Route::post('/homepage', [Admin\SettingsController::class, 'homepageSave']);
            Route::get('/social', [Admin\SettingsController::class, 'social'])->name('social');
            Route::post('/social', [Admin\SettingsController::class, 'socialSave']);
            Route::get('/featured', [Admin\SettingsController::class, 'featured'])->name('featured');
            Route::post('/featured', [Admin\SettingsController::class, 'featuredSave']);
            Route::get('/resetting', [Admin\SettingsController::class, 'resetting'])->name('resetting');
            Route::post('/resetting', [Admin\SettingsController::class, 'resettingSave']);
            Route::get('/currency', [Admin\SettingsController::class, 'currencyManagement'])->name('currencyManagement');
            Route::post('/currency', [Admin\SettingsController::class, 'currencyManagementSave'])->name('currencyManagementSave');
            Route::get('/auth_type', [Admin\SettingsController::class, 'authorizationType'])->name('authType');
            Route::post('/auth_type', [Admin\SettingsController::class, 'authorizationTypeSave'])->name('authTypeSave');
            Route::post('/resetDonationGoal', [Admin\SettingsController::class, 'resetDonationGoal'])->name('resetDonationGoal');
            Route::post('/resetTopDonator', [Admin\SettingsController::class, 'resetTopDonator'])->name('resetTopDonator');
            Route::post('/removeAllPayments', [Admin\SettingsController::class, 'removeAllPayments'])->name('removeAllPayments');
            Route::post('/removeAllUsers', [Admin\SettingsController::class, 'removeAllUsers'])->name('removeAllUsers');
            Route::post('/resetBanlist', [Admin\SettingsController::class, 'resetBanlist'])->name('resetBanlist');
            Route::post('/removeAllPlayerdata', [Admin\SettingsController::class, 'removeAllPlayerdata'])->name('removeAllPlayerdata');
            Route::post('/fullWipe', [Admin\SettingsController::class, 'fullWipe'])->name('fullWipe');
            Route::post('/checkServer', [Admin\SettingsController::class, 'checkServer'])->name('checkServer');
        });

        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/getByFilter', [Admin\StatisticsController::class, 'getByFilter'])->name('getByFilter');
            Route::get('/', [Admin\StatisticsController::class, 'index'])->name('index');
        });

        Route::resource('taxes', Admin\TaxesController::class)->except('show');

        // Promoted Packages
        Route::resource('promoted', Admin\PromotedItemsController::class)
            ->only(['index', 'create', 'store', 'destroy']);
        Route::post('/promoted/settings', [Admin\PromotedItemsController::class, 'settings'])
            ->name('promoted.settings');

        Route::prefix('globalCommands')->name('globalCommands.')->group(function () {
            Route::get('/', [Admin\GlobalCommandsController::class, 'index'])->name('index');
            Route::post('/delete/{id}', [Admin\GlobalCommandsController::class, 'delete']);
            Route::post('/', [Admin\GlobalCommandsController::class, 'save']);
        });

        Route::prefix('chargeback')->name('chargeback.')->group(function () {
            Route::get('/settings', [Admin\ChargebackController::class, 'settings'])->name('settings');
            Route::post('/settings', [Admin\ChargebackController::class, 'settingsSave']);
            Route::get('/{id}/download', [Admin\ChargebackController::class, 'download'])->name('download');
            Route::get('/spendinglimit', [Admin\ChargebackController::class, 'spendinglimit'])->name('spendinglimit');
            Route::post('/spendinglimit', [Admin\ChargebackController::class, 'spendinglimitSave']);
        });
        Route::resource('chargeback', Admin\ChargebackController::class)->only(['index', 'show']);

        Route::prefix('lookup')->name('lookup.')->group(function () {
            Route::get('/', [Admin\LookupController::class, 'index'])->name('index');
            Route::get('/{username}', [Admin\LookupController::class, 'search'])->name('search');
        });

        Route::prefix('ipchecks')->name('ipchecks.')->group(function () {
            Route::get('/', [Admin\IPChecksController::class, 'settings'])->name('index');
            Route::post('/', [Admin\IPChecksController::class, 'settingsSave']);
        });

        Route::prefix('apiAccessSettings')->name('apiAccessSettings.')->group(function () {
            Route::get('/', [Admin\ApiAccessController::class, 'index'])->name('index');
            Route::post('/', [Admin\ApiAccessController::class, 'save']);
        });

        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/datatables', [Admin\SubscriptionsController::class, 'datatables'])->name('datatables');
            Route::get('/', [Admin\SubscriptionsController::class, 'index'])->name('index');
        });

        Route::prefix('themes')->name('themes.')->group(function () {
            Route::get('/', [Admin\ThemesController::class, 'index'])->name('index');
            Route::get('/settings/{themeId}', [Admin\ThemesController::class, 'settings'])->name('settings');
            Route::post('/settings/{themeId}', [Admin\ThemesController::class, 'saveSettings'])->name('saveSettings');
            Route::post('/files/saveFile/{themeId}/{filePath}', [Admin\ThemesController::class, 'saveFile'])->name('saveFile')->where('filePath', '(.*)');
            Route::get('/files/readFile/{themeId}/{filePath}', [Admin\ThemesController::class, 'readFile'])->name('readFile')->where('filePath', '(.*)');
            Route::post('/files/deleteFile/{themeId}/{filePath}', [Admin\ThemesController::class, 'delFile'])->name('delFile')->where('filePath', '(.*)');
            Route::get('/files/{themeId}', [Admin\ThemesController::class, 'files'])->name('files');
            Route::post('/files/{themeId}', [Admin\ThemesController::class, 'uploadFile'])->name('uploadFile');
            Route::post('/create', [Admin\ThemesController::class, 'create']);
            Route::post('/upgrade/{themeId}', [Admin\ThemesController::class, 'upgrade']);
            Route::post('/install/{themeId}', [Admin\ThemesController::class, 'install']);
            Route::post('/toggleDeveloperMode/{themeId}', [Admin\ThemesController::class, 'toggleDeveloperMode']);
            Route::post('/build/{themeId}', [Admin\ThemesController::class, 'build']);
            Route::post('/delete/{themeId}', [Admin\ThemesController::class, 'delete']);
            Route::get('/logs', [Admin\ThemesController::class, 'logs'])->name('logs');
        });

        Route::prefix('securityLogs')->name('securityLogs.')->group(function () {
            Route::get('/', [Admin\SecurityLogController::class, 'index'])->name('index');
        });

        Route::get('/profile/setOTP/{code}', [Admin\AdminProfileController::class, 'setOTP'])->name('setOTP');
        Route::resource('profile', Admin\AdminProfileController::class)
            ->only(['index', 'store']);
        Route::post('/profile/changePassword', [Admin\AdminProfileController::class, 'changePassword'])->name('profile.changePassword');
    });
});

Route::prefix('api/admin')->middleware(['installed', 'admin'])->name('api.')->group(function () {
    // Update staff settings
    Route::patch('/pages/staff', [Admin\Api\PagesController::class, 'staffUpdate']);
    // Update staff sorting
    Route::patch('/pages/staff/update-sort', [Admin\Api\PagesController::class, 'updateStaffSort']);
    // Get banned users, ban and unban user
    Route::apiResource('banlist', Admin\Api\BansController::class)->only(['index', 'store', 'destroy']);
    // Get whitelisted users, add and remove user in whitelist
    Route::apiResource('whitelist', Admin\Api\WhitelistController::class)->only(['index', 'store', 'destroy']);
    // Delete and Update Referral Settings
    Route::patch('/refs/enabledUpdate', [Admin\Api\RefsController::class, 'enabledSave']);
    Route::apiResource('refs', Admin\Api\RefsController::class)->only(['index', 'destroy']);
    // Admins (Users)
    Route::apiResource('users', Admin\Api\AdminController::class)->only(['index', 'destroy']);
    Route::apiResource('securityLogs', Admin\Api\SecurityLogsController::class)->only(['index']);
    // Get and Update Coupons
    Route::apiResource('coupons', Admin\Api\CouponsController::class)->only(['index', 'destroy']);
    // Get and Remove Giftcards
    Route::apiResource('giftcards', Admin\Api\GiftcardsController::class)->only(['index', 'destroy']);

    Route::prefix('settings')->name('settings.')->group(function () {
        // Update merchant settings
        Route::patch('/merchant/{merchant}', [Admin\Api\Settings\MerchantSettingsController::class, 'update'])->name('merchant');
        // Servers Management
        Route::apiResource('server', Admin\Api\Settings\ServerSettingsController::class)->only(['store', 'update', 'destroy']);
        Route::post('/servers/check/{id}', [Admin\Api\Settings\ServerSettingsController::class, 'check'])->name('servers.check');
        Route::post('/email/check', [Admin\Api\Settings\EmailSettingsController::class, 'check'])->name('email.check');
    });

    Route::apiResource('payments', Admin\Api\PaymentsController::class)->only(['index', 'store', 'destroy']);
	Route::patch('/payments/enabledUpdate', [Admin\Api\PaymentsController::class, 'enabledSave']);
    // To re-delivery items
    Route::post('/payments/{id}/delivery', [Admin\Api\PaymentsController::class, 'delivery']);
    // Add payment note
    Route::post('/payments/{id}/note', [Admin\Api\PaymentsController::class, 'note']);

    Route::apiResource('chargeback', Admin\Api\ChargebackController::class)->only(['index', 'store', 'destroy']);
    // To mark a chargeback as done
    Route::patch('/chargeback/{id}/finish', [Admin\Api\ChargebackController::class, 'finish']);
    Route::post('/chargeback/{id}/submit', [Admin\Api\ChargebackController::class, 'submit']);

    // Read and delete notifications
    Route::post('/notifications/read-all', [Admin\Api\NotificationsController::class, 'readAll']);
    Route::post('/notifications/{id}/read', [Admin\Api\NotificationsController::class, 'read']);
    Route::delete('/notifications/{id}', [Admin\Api\NotificationsController::class, 'destroy']);

    // Statistics
    Route::get('/statistics/income-by-players', [Admin\Api\StatsController::class, 'topPlayers']);
    Route::get('/statistics/total-revenue', [Admin\Api\StatsController::class, 'totalRevenue']);
});

Route::get('/install', [InstallController::class, 'index']);
Route::post('/install/changeLicenseKey', [InstallController::class, 'changeLicenseKey']);
Route::get('/install/diagnose', [InstallController::class, 'diagnose']);
Route::post('/initiateInstallation', [InstallController::class, 'install']);

Route::any('/error', function () {
    $settings = Setting::query()->select('site_name', 'site_desc')->find(1);

    return view('welcome', ['site_name' => $settings->site_name, 'site_desc' => $settings->site_desc]);
});
Route::any('/success', function () {
    $settings = Setting::query()->select('site_name', 'site_desc')->find(1);

    return view('welcome', ['site_name' => $settings->site_name, 'site_desc' => $settings->site_desc]);
});

Route::any('/profile', [PagesController::class, 'index'])->middleware('installed');
