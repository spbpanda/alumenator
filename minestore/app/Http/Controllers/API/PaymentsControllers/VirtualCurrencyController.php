<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\API\VirtualCurrencyPaymentRequest;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\PlayerData;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VirtualCurrencyController extends Controller
{
    /**
     * Creates a virtual currency payment.
     *
     * @param Cart $cart The cart item containing the purchase details.
     * @param Payment $payment The payment details.
     * @return array An array containing the success status and additional data or message.
     * @throws \Throwable If the transaction fails.
     */
    public static function create(Cart $cart, Payment $payment): array
    {
        $settings = Setting::select(['is_api', 'api_secret', 'virtual_currency'])->find(1);
        if (empty($settings)) {
            return self::errorResponse('Unable to find the settings.');
        }

        if ($settings->is_api == 0) {
            return self::errorResponse('API is disabled. Please enable it in the settings.');
        }

        $user = User::where('id', $cart->user_id)->first();

        $playerData = PlayerData::where('username', $user->username)
            ->select('balance')
            ->first();

        // Check if the player has enough virtual currency to purchase
        if (empty($playerData)) {
            return self::errorResponse('Unable to find your virtual currency balance on the server.');
        }

        // Check if the player has enough virtual currency to purchase the item
        if ($playerData->balance < $cart->virtual_price) {
            return self::errorResponse('You do not have enough virtual currency to purchase.');
        }

        // Generate SHA-256 signature
        $data = [
            'username' => $user->username,
            'price' => $cart->virtual_price,
            'payment_internal_id' => $payment->internal_id,
        ];

        $secretKey = $settings->api_secret ?? '';
        $signature = self::generateSha256Signature($data, $secretKey);

        // Sending the request to check the real-time balance of the player on the server
        ItemsController::chargeVirtualCurrencyBalanceCommand([
            'username' => $user->username,
            'price' => $cart->virtual_price,
            'payment_id' => $payment->id,
            'payment_internal_id' => $payment->internal_id,
            'cart_id' => $cart->id,
            'signature' => $signature,
        ]);

        $cart->update([
            'is_active' => 0,
        ]);

        $payment->update([
            'price' => $cart->virtual_price,
            'gateway' => 'Virtual Currency',
            'currency' => $settings->virtual_currency ?? 'VC',
        ]);

        DB::commit();

        return [
            'type' => 'url',
            'url' => 'https://' . request()->getHost() . '/payment/flow?order_id=' . $payment->internal_id,
            'order_id' => $payment->internal_id,
        ];
    }

    /**
     * Handles the virtual currency payment.
     *
     * @param string $api_key The API key to authenticate the request.
     * @param VirtualCurrencyPaymentRequest $request The request containing the payment details.
     * @return JsonResponse
     */
    public static function handle(string $api_key, VirtualCurrencyPaymentRequest $request): JsonResponse
    {
        // Checking if the API is enabled and the API key is correct
        $settings = Setting::select(['is_api', 'api_secret'])->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        // Fetching the payment details
        $payment_internal_id = $request->get('data')['payment_internal_id'];
        $username = $request->get('data')['username'];
        $payment = Payment::where('internal_id', $payment_internal_id)->first();

        if (empty($payment)) {
            return self::errorJsonResponse('Payment not found.');
        }

        // Checking if the signature is valid
        $secretKey = Setting::find(1)->select('api_secret')->first()->api_secret;
        $data = [
            'username' => $request->get('data')['username'],
            'price' => $request->get('data')['price'],
            'payment_internal_id' => $request->get('data')['payment_internal_id'],
        ];

        $signature = self::generateSha256Signature($data, $secretKey);

        if ($signature !== $request->get('data')['signature']) {
            return self::errorJsonResponse('Invalid signature.');
        }

        // Checking if payment amount is the same
        if ($payment->price != $request->get('data')['price']) {
            return self::errorJsonResponse('Invalid payment amount.');
        }

        if ($payment->gateway !== 'Virtual Currency') {
            return self::errorJsonResponse('Invalid payment gateway.');
        }

        if ($request->get('status') === 'success') {
            // Deducting the virtual currency from the player's account
            $player = PlayerData::where('username', $username)->first();
            $remaining_balance = $request->get('data')['remaining_balance'];
            $player->update([
                'balance' => $remaining_balance,
            ]);

            $payment->update([
                'status' => Payment::PROCESSED,
            ]);

            $paymentController = new PaymentController();
            $paymentController->FinalHandler($payment->id);
        } else {
            $payment->update([
                'status' => Payment::ERROR,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment failed.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment successful.',
        ]);
    }

    /**
     * Generates SHA-256 signature for the given data.
     *
     * @param array $data
     * @param string $secretKey
     * @return string
     */
    private static function generateSha256Signature(array $data, string $secretKey): string
    {
        // Sort data by keys to ensure consistent ordering
        ksort($data);
        $queryString = http_build_query($data);

        return hash_hmac('sha256', $queryString, $secretKey);
    }

    private static function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => __($message),
        ];
    }

    private static function errorJsonResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ]);
    }
}
