<?php

use App\Http\Controllers\AdvertController;
use App\Http\Controllers\API\PaymentCheckingController;
use App\Http\Controllers\API\PaymentsControllers\PayPalIPNController;
use App\Http\Controllers\API\PaymentsControllers\PhonepeController;
use App\Http\Controllers\API\PaymentsControllers\PixController;
use App\Http\Controllers\API\PaymentsControllers\SepayController;
use App\Http\Controllers\API\PaymentsControllers\UnitPayController;
use App\Http\Controllers\API\PaymentsControllers\VirtualCurrencyController;
use App\Http\Controllers\API\REST\RestOrderController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Discord\DiscordAuthController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('servers')->group(function () {
    Route::GET('/{secret}/commands/queue', [SettingsController::class, 'commandsQueue']);
    Route::POST('/{secret}/commands/delivered', [SettingsController::class, 'commandsDelivered']);
    Route::POST('/{secret}/commands/executed', [SettingsController::class, 'commandsExecuted']);
    Route::POST('/{secret}/commands/validated', [SettingsController::class, 'validateCommandInQueue']);
});

Route::prefix('game_auth')->group(function () {
    Route::POST('/init/{username}', [AuthController::class, 'gameAuthInit']);
    Route::POST('/check/{username}', [AuthController::class, 'gameAuthCheck']);
    Route::POST('/confirm/{auth_id}', [AuthController::class, 'gameAuthConfirm']);
});

Route::prefix('/auth')->group(function () {
    Route::get('/discord', [DiscordAuthController::class, 'redirect']);
    Route::get('/discord/callback', [DiscordAuthController::class, 'callback']);
});

Route::GET('/staff', [SettingsController::class, 'getStaff']);
Route::GET('/profile/{username}', [SettingsController::class, 'getProfile']);

Route::GET('/getVersion', [SettingsController::class, 'getMineStoreVersion']);
Route::GET('/{api_key}/getMostRecent', [SettingsController::class, 'getMostRecent']);
Route::GET('/{api_key}/getMainCurrency', [SettingsController::class, 'getMainCurrency']);
Route::GET('/{api_key}/getTotalPayments', [SettingsController::class, 'getTotalPayments']);
Route::GET('/{api_key}/getTotalPaymentsPaged', [SettingsController::class, 'getTotalPaymentsPaged']);
Route::GET('/{api_key}/getDetailedPayments', [SettingsController::class, 'getDetailedPayments']);
Route::GET('/{api_key}/getGeneralInformation', [SettingsController::class, 'getGeneralInformation']);
Route::GET('/{api_key}/top_donators', [SettingsController::class, 'getTopDonators']);
Route::GET('/{api_key}/donation_goal', [SettingsController::class, 'getDonationGoal']);
Route::GET('/{api_key}/user_info/{username}', [SettingsController::class, 'getUserInfo']);
Route::GET('/{api_key}/getPackage', [SettingsController::class, 'getPackage']);
Route::GET('/{api_key}/validGiftCard', [SettingsController::class, 'validGiftCard']);
Route::POST('/{api_key}/createGiftCard', [SettingsController::class, 'createGiftCard']);
Route::GET('/{api_key}/referrersList', [SettingsController::class, 'referrersList']);
Route::GET('/{api_key}/couponList', [SettingsController::class, 'couponList']);
Route::GET('/{api_key}/bans', [SettingsController::class, 'bansList']);
Route::POST('/{api_key}/addBan', [SettingsController::class, 'addBan']);
Route::POST('/{api_key}/removeBan', [SettingsController::class, 'removeBan']);
Route::POST('/{api_key}/in-game/manageSubscriptions', [SubscriptionController::class, 'manageSubscription']);

Route::prefix('{api_key}/gui')->group(function () {
    Route::GET('/packages_new', [SettingsController::class, 'getPackagesNew']);
    Route::GET('/packages', [SettingsController::class, 'getPackages']); // old route (before gui release)
    Route::GET('/categories', [SettingsController::class, 'getCategories']);
    Route::GET('/subcategories', [SettingsController::class, 'getSubCategories']);
});

Route::prefix('/auth')->group(function () {
    Route::POST('/username', [AuthController::class, 'username']);
});

Route::prefix('payments')->group(function () {
    Route::POST('/handle/paypal', [PaymentController::class, 'paypalHandle']);
    Route::POST('/handle/paypalIPN', [PayPalIPNController::class, 'handle']);
    Route::POST('/handle/coinpayments', [PaymentController::class, 'CoinpaymentsHandle']);
    Route::POST('/handle/g2a', [PaymentController::class, 'G2AHandle']);
    Route::POST('/handle/stripe', [PaymentController::class, 'StripeHandle']);
    Route::ANY('/handle/terminal3', [PaymentController::class, 'Terminal3Handle']);
    Route::POST('/handle/mollie', [PaymentController::class, 'MollieHandle']);
    Route::POST('/handle/paytm', [PaymentController::class, 'PaytmHandle']);
    Route::POST('/handle/paygol', [PaymentController::class, 'PaygolHandle']);
    Route::POST('/handle/cashfree', [PaymentController::class, 'CashFreeHandle']);
    Route::POST('/handle/mercadopago', [PaymentController::class, 'MercadoPagoHandle']);
    Route::GET('/handle/gopay', [PaymentController::class, 'GoPayHandle']);
    Route::POST('/handle/razorPay', [PaymentController::class, 'RazorPayHandle']);
    Route::GET('/handle/unitpay', [UnitPayController::class, 'handle']);
    Route::POST('/handle/freekassa', [PaymentController::class, 'FreeKassaHandle']);
    Route::POST('/handle/qiwi', [PaymentController::class, 'QiwiHandle']);
    Route::POST('/handle/enot', [PaymentController::class, 'EnotHandle']);
    Route::POST('/handle/payu', [PaymentController::class, 'PayUHandle']);
    Route::POST('/handle/payuindia', [PaymentController::class, 'PayUIndiaHandle']);
    Route::POST('/handle/hotpay', [PaymentController::class, 'HotPayHandle']);
    Route::POST('/handle/interkassa', [PaymentController::class, 'InterkassaHandle']);
    Route::POST('/handle/coinbase', [PaymentController::class, 'CoinbaseHandle']);
    Route::POST('/handle/skrill', [PaymentController::class, 'SkrillHandle']);
    Route::POST('/handle/fondy', [PaymentController::class, 'FondyHandle']);
    Route::POST('/handle/midtrans', [PaymentController::class, 'MidtransHandle']);
    Route::POST('/handle/cordarium', [PaymentController::class, 'CordariumHandle']);
    Route::POST('/handle/paytr', [PaymentController::class, 'PayTRHandle']);
    Route::POST('/handle/tbank', [PaymentController::class, 'TBankHandle']);
    Route::POST('/handle/sepay', [SepayController::class, 'handle']);
    Route::POST('/handle/sepay/check', [SepayController::class, 'check']);
    Route::POST('/handle/phonepe', [PhonepeController::class, 'handle']);
    Route::POST('/handle/{api_key}/virtualcurrency', [VirtualCurrencyController::class, 'handle']);
    Route::POST('/handle/pix/check', [PixController::class, 'check']);
});

Route::POST('/server/notify', [ServerController::class, 'handleNotification'])->middleware('authorize');

Route::prefix('pages')->group(function () {
    Route::POST('/get', [PagesController::class, 'get']);
});

Route::prefix('announcement')->group(function () {
    Route::GET('/get', [AdvertController::class, 'get']);
});

Route::prefix('settings')->group(function () {
    Route::GET('get', [SettingsController::class, 'get']);
});

Route::prefix('items')->group(function () {
    Route::POST('/get/guest/{id}', [ItemsController::class, 'getOneGuest']);
    Route::ANY('/getFeaturedDeals', [ItemsController::class, 'getFeaturedDeals']);
});

Route::ANY('/categories/get', [CategoriesController::class, 'get']);
Route::POST('/cart/getGift', [CartController::class, 'getGift']); // Get giftcard value for the index page (v3.x) (don't need auth)
Route::GET('/patrons/get', [SettingsController::class, 'getPatrons']);

Route::prefix('rest/v2')->group(function () {
    Route::POST('{api_key}/payment/create', [RestOrderController::class, 'create']);
});

Route::middleware('auth:api')->group(function () {
    Route::POST('/user', [SettingsController::class, 'getUser']);

    Route::POST('/payments/checkStatus', [PaymentCheckingController::class, 'check']);
    Route::POST('/items/get/{id}', [ItemsController::class, 'getOne']);

    Route::post('/categories/get/{url}', [CategoriesController::class, 'getOne'])
        ->where('url', '[a-zA-Z0-9\-\_\/]+');

    Route::prefix('/cart')->group(function () {
        Route::POST('/get', [CartController::class, 'get']);
        Route::POST('/add/{id}', [CartController::class, 'addItem']);
        Route::POST('/remove/{id}', [CartController::class, 'removeItem']);
        Route::POST('/reload/{id}', [CartController::class, 'reloadItem']);
        Route::POST('/changePrice/{id}', [CartController::class, 'changeCustomPrice']); // Change custom price for item in cart (v3.x)
        Route::POST('/setVariable/{id}', [CartController::class, 'setVariable']); // Set variable for item in cart (v3.x)
        Route::POST('/setSelectedServer/{id}', [CartController::class, 'setSelectedServer']); // Set selected server for item in cart (v3.x)
        Route::POST('/setReferral', [CartController::class, 'setReferral']); // Set referral for a cart (v3.x)
        Route::POST('/removeReferral', [CartController::class, 'removeReferral']); // Remove referral from the cart (v3.x)
        Route::POST('/acceptCoupon', [CartController::class, 'acceptCoupon']);
        Route::POST('/getCoupon', [CartController::class, 'getCoupon']);
        //Route::POST('/getGift', [CartController::class, 'getGift']);
        Route::POST('/removeCoupon', [CartController::class, 'removeCoupon']); // Remove coupon (v3.x)
        Route::POST('/removeGiftcard', [CartController::class, 'removeGiftcard']); // Remove giftcard (v3.x)
        Route::POST('/getSelectServers', [CartController::class, 'getSelectServers']); // Get Servers to select for specific items at the checkout (v3.x)
        Route::POST('/getPromoted', [CartController::class, 'getPromoted']); // Get promoted items for checkout (v3.x)
    });

    Route::prefix('payments')->group(function () {
        Route::POST('/get', [PaymentController::class, 'get']);
        Route::POST('/create', [PaymentController::class, 'create']);
    });
});
