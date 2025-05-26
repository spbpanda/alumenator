<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App;
use App\Helpers\PayNowHelper;
use App\Helpers\SubscriptionHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Integrations\PayNow\Management;
use App\Models\Cart;
use App\Models\Chargeback;
use App\Models\Payment;
use App\Models\PnCheckoutReference;
use App\Models\PnWebhook;
use App\Models\Subscription;
use Crypt;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayNowController extends Controller
{
    private const TOLERANCE_PERIOD = 5 * 60 * 1000; // 5 minutes
    private const LOG_ENABLED = true;

    protected PaymentController $paymentController;

    public function __construct()
    {
        $this->paymentController = new PaymentController();
    }

    /**
     * Create a PayNow checkout for the given cart and payment.
     *
     * @param Cart $cart
     * @param Payment $payment
     * @param string $currency
     * @param bool $isSubs
     * @return array|JsonResponse
     */
    public function create(Cart $cart, Payment $payment, string $currency, bool $isSubs)
    {
        $paynowHelper = app(PayNowHelper::class);
        $paynowManagement = app(Management::class);

        $integration = $paynowHelper->checkPayNowIntegrationStatus();
        if (!$integration) {
            return $this->returnErrorResponse('PayNow integration is not enabled.');
        }

        $currencyValidation = $paynowHelper->validateCurrency();
        if (!$currencyValidation) {
            return $this->returnErrorResponse('Primary webstore currency might match PayNow currency.');
        }

        $validateItems = $paynowHelper->validateItems($cart);
        if (!$validateItems) {
            return $this->returnErrorResponse('You can\'t checkout custom price items with PayNow.');
        }

        $lines = $paynowHelper->getLines($cart->cart_items);
        if (empty($lines)) {
            return $this->returnErrorResponse('No valid products found for checkout.');
        }

        $cart->update([
            'is_active' => 0,
        ]);

        $paynowHelper->convertCurrency($payment, $cart, $currency);
        $data = $paynowHelper->generateData($lines, $cart, $isSubs, $payment);

        $checkout = $paynowManagement->createCheckout($data);
        if (!$checkout) {
            return $this->returnErrorResponse('Failed to create PayNow checkout.');
        }

        PnCheckoutReference::create([
            'payment_id' => $payment->id,
            'checkout_id' => $checkout['id'],
        ]);

        if ($isSubs) {
            $paynowHelper->createSubscription($cart, $payment);
        }

        return [
            'type' => 'url',
            'url' => $checkout['url'] . '&disable_promo_codes=true',
        ];
    }

    public function handle(Request $request)
    {
        $paynowHelper = app(PayNowHelper::class);
        // Step 0: Check if PayNow payment method is enabled
        $integration = $paynowHelper->checkPayNowIntegrationStatus();
        if (!$integration) {
            Log::warning('PayNow Webhook: Integration not enabled', ['request' => $request->all()]);
            abort(400, json_encode(['status' => false, 'message' => 'PayNow integration is not enabled.']));
        }

        // Get configuration
        $config = PnWebhook::firstOrFail();
        $signingSecret = Crypt::decryptString($config->secret);

        if (!$signingSecret) {
            Log::error('PayNow Webhook: Missing signing secret in configuration');
            abort(500, json_encode(['error' => 'Invalid configuration']));
        }

        // Step 1: Retrieve headers and payload
        $rawBody = $request->getContent();
        $timestamp = $request->header('PayNow-Timestamp');
        $providedSignature = $request->header('PayNow-Signature');

        // Verify required headers
        if (!$timestamp || !$providedSignature) {
            Log::warning('PayNow Webhook: Missing required headers', ['headers' => $request->headers->all()]);
            abort(400, json_encode(['error' => 'Missing required headers']));
        }

        // Step 2: Verify timestamp
        $timestampInt = (int)$timestamp;
        if (is_nan($timestampInt)) {
            Log::warning('PayNow Webhook: Invalid timestamp format');
            abort(400, json_encode(['error' => 'Invalid timestamp format']));
        }

        $currentTime = now()->getTimestampMs();
        if ($currentTime - $timestampInt > self::TOLERANCE_PERIOD) {
            Log::warning('PayNow Webhook: Timestamp out of tolerance');
            abort(401, json_encode(['error' => 'Timestamp out of tolerance']));
        }

        // Step 3: Verify HMAC signature
        $payloadWithTimestamp = "{$timestamp}.{$rawBody}";
        $expectedSignature = base64_encode(hash_hmac('sha256', $payloadWithTimestamp, $signingSecret, true));

        if (!$this->isSignatureValid($providedSignature, $expectedSignature)) {
            Log::warning('PayNow Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Step 4: Process the webhook
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;
        $data = $payload['body'] ?? [];

        // Extract relevant data from payload
        $checkoutId = $data['checkout_id'] ?? null;

        $payment = $this->getPayment($checkoutId);
        if (!$payment) {
            Log::warning('PayNow Webhook: Payment not found for checkout ID', ['checkout_id' => $checkoutId]);
            abort(404, json_encode(['error' => 'Payment not found']));
        }

        $this->actualizePaymentDetails($payment, $data);

        try {
            switch ($eventType) {
                case 'ON_ORDER_COMPLETED':
                    $response = $this->handleOnOrderCompleted($payment, $data);
                    break;

                case 'ON_REFUND':
                    $response = $this->handleOnRefund($payment, $data);
                    break;

                case 'ON_CHARGEBACK':
                    $response = $this->handleChargeback($payment, $data);
                    break;

                default:
                    Log::info("PayNow Webhook: Unhandled event type: {$eventType}", ['payload' => $payload]);
                    $response = response()->json(['status' => 'success', 'message' => 'Event processed'], 200);
                    break;
            }
            return $response;
        } catch (\Exception $e) {
            Log::error("PayNow Webhook: Error processing event {$eventType}: {$e->getMessage()}", ['payload' => $payload]);
            abort(500, json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]));
        }
    }

    /**
     * Verify signature using timing safe comparison
     */
    private function isSignatureValid(string $providedSignature, string $expectedSignature): bool
    {
        return hash_equals(
            base64_decode($providedSignature),
            base64_decode($expectedSignature)
        );
    }

    /**
     * Handle ON_ORDER_COMPLETED event
     */
    private function handleOnOrderCompleted(Payment $payment, array $data): \Illuminate\Http\JsonResponse
    {
        if (self::LOG_ENABLED) {
            Log::info('PayNow Webhook: Order Completed', [
                'payment_id' => $payment->id,
                'data' => $data
            ]);
        }

        $payNowPayment = $this->validatePayNowOrder($data, $payment);
        if (!$payNowPayment) {
            Log::warning('PayNow Webhook: Order validation failed', ['payment_id' => $payment->id]);
            return response()->json(['error' => 'PayNow order validation failed'], 400);
        }

        if ($payNowPayment['status'] !== 'completed') {
            if (self::LOG_ENABLED) {
                Log::info('PayNow Webhook: Order not completed', [
                    'payment_id' => $payment->id,
                    'data' => $data
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Order not in completed state'], 200);
        }

        if (isset($payNowPayment['tax_inclusive']) && $payNowPayment['tax_inclusive']) {
            $payment->update([
                'tax_inclusive' => 1,
            ]);
        }

        if (isset($payNowPayment['tax_amount']) && $payNowPayment['tax_amount'] > 0) {
            $cart = Cart::find($payment->cart_id);
            $cart?->update([
                'tax' => $payNowPayment['tax_amount'] / 100,
            ]);
        }

        if (isset($payNowPayment['pretty_id']) && $payNowPayment['pretty_id']) {
            $payment->update([
                'transaction' => $payNowPayment['pretty_id'],
            ]);
        }

        $subscription = null;
        if (isset($payNowPayment['subscription_id']) && $payNowPayment['subscription_id']) {
            $subscription = Subscription::where('payment_id', $payment->id)
                ->first();

            if ($subscription) {
                $subscription->update([
                    'sid' => $payNowPayment['subscription_id'],
                    'status' => Subscription::ACTIVE,
                    'count' => $subscription->count + 1,
                    'renewal' => now()->addDays($subscription->interval_days)->format('Y-m-d'),
                ]);

                $payment->update([
                    'internal_subscription_id' => $subscription->id,
                ]);
            }
        }

        if ($subscription && $subscription->count > 1) {
            SubscriptionHelper::replicatePaymentRecord($payment, $subscription);
            $expire = $this->paymentController->RenewHandler($payment->id);
            if ($expire) {
                Log::info('PayNow Webhook: Payment renewed', ['payment_id' => $payment->id]);
                exit('Payment renewed');
            }
            Log::error('PayNow Webhook: Payment renewal failed', ['payment_id' => $payment->id]);

            return response()->json(['status' => 'error', 'message' => 'Payment renewal failed'], 500);
        } else {
            $final = $this->paymentController->FinalHandler($payment->id);
            if ($final) {
                Log::info('PayNow Webhook: Payment completed', ['payment_id' => $payment->id]);
                exit('Payment completed');
            }
            Log::error('PayNow Webhook: Payment completion failed', ['payment_id' => $payment->id]);
            return response()->json(['status' => 'error', 'message' => 'Payment completion failed'], 500);
        }
    }

    /**
     * Handle ON_REFUND event
     */
    private function handleOnRefund(Payment $payment, array $data): \Illuminate\Http\JsonResponse
    {
        if (self::LOG_ENABLED) {
            Log::info('PayNow Webhook: Order Refunded', [
                'payment_id' => $payment->id,
                'data' => $data
            ]);
        }

        $payNowPayment = $this->validatePayNowOrder($data, $payment);
        if (!$payNowPayment) {
            Log::warning('PayNow Webhook: Order validation failed', ['payment_id' => $payment->id]);
            return response()->json(['error' => 'PayNow order validation failed'], 400);
        }

        if ($payNowPayment['status'] !== 'refunded') {
            Log::info('PayNow Webhook: Order not refunded', [
                'payment_id' => $payment->id,
                'data' => $data
            ]);
            exit('Order not refunded');
        }

        if ($this->paymentController::ExpireHandler($payment->id)) {
            Log::info('PayNow Webhook: Refund processed', ['payment_id' => $payment->id]);
            exit('Refund processed');
        }

        Log::error('PayNow Webhook: Refund processing failed', ['payment_id' => $payment->id]);
        return response()->json(['status' => 'error', 'message' => 'Refund processing failed'], 500);
    }

    /**
     * Handle ON_CHARGEBACK event
     */
    private function handleChargeback(Payment $payment, array $data): \Illuminate\Http\JsonResponse
    {
        if (self::LOG_ENABLED) {
            Log::info('PayNow Webhook: Chargeback event', [
                'payment_id' => $payment->id,
                'data' => $data
            ]);
        }

        $payNowPayment = $this->validatePayNowOrder($data, $payment);
        if (!$payNowPayment) {
            Log::warning('PayNow Webhook: Order validation failed', ['payment_id' => $payment->id]);
            return response()->json(['error' => 'PayNow order validation failed'], 400);
        }

        $payment->update([
            'status' => Payment::CHARGEBACK,
        ]);

        $subscription = Subscription::where('payment_id', $payment->id)
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => Subscription::CANCELLED,
            ]);
        }

        Chargeback::updateOrCreate(
            ['sid' => $data['id']],
            [
                'payment_id' => $payment->id,
                'status' => Chargeback::COMPLETED,
                'details' => json_encode([
                    'case_id' => $data['id'],
                    'case_type' => 'chargeback',
                    'status' => 'Won',
                    'reason' => 'Automatically handled by PayNow Chargeback Protection'
                ]),
            ]
        );

        $this->paymentController->PaymentHandler($payment->id);
        if ($this->paymentController->ChargebackHandler($payment->id)) {
            Log::info('PayNow Webhook: Chargeback processed', ['payment_id' => $payment->id]);
            exit('Chargeback processed');
        }

        Log::error('PayNow Webhook: Chargeback processing failed', ['payment_id' => $payment->id]);
        exit('Chargeback processing failed');
    }

    /**
     * Update the payment details with the billing email if it is not already set.
     *
     * @param Payment $payment The payment object to update
     * @param array $data The data array containing billing_email
     * @return void This function does not return anything.
     */
    private function actualizePaymentDetails(Payment $payment, array $data): void
    {
        if (!isset($data['billing_email'])) {
            return;
        }

        $paymentDetails = json_decode($payment->details, true);

        if (!isset($paymentDetails['email'])) {
            $paymentDetails['email'] = $data['billing_email'];
            $paymentDetails['fullname'] = $data['billing_name'] ?? 'N/A';
            $paymentDetails['country'] = $data['billing_address_country'] ?? 'N/A';
            $payment->details = json_encode($paymentDetails);
        }

        if (isset($data['id'])) {
            $payment->transaction = $data['id'];
        }

        $payment->gateway = 'PayNow';
        $payment->save();
    }

    /**
     * Retrieves the payment associated with a given checkout ID.
     *
     * This method queries the `PnCheckoutReference` model to find a record
     * matching the provided checkout ID. It then loads the associated payment
     * and returns it. If no matching record is found, null is returned.
     *
     * @param string $checkoutId The ID of the checkout to retrieve the payment for.
     * @return Payment|null The associated payment object, or null if not found.
     */
    private function getPayment(string $checkoutId): ?Payment
    {
        return PnCheckoutReference::where('checkout_id', $checkoutId)
            ->with('payment')
            ->first()
            ?->payment;
    }

    /**
     * Validates a PayNow order by retrieving its details from the PayNow API.
     *
     * This method uses the provided data to fetch the order details from the PayNow API.
     * If the order is not found, an error is logged, and null is returned.
     * Otherwise, the retrieved order details are returned.
     *
     * @param array $data The data array containing the order ID.
     * @param Payment $payment The payment object associated with the order.
     * @return array|null The PayNow order details if found, or null if not found.
     */
    private function validatePayNowOrder(array $data, Payment $payment)
    {
        $management = App::make(Management::class);
        $payNowPayment = $management->getOrder($data['id']);
        if (!$payNowPayment) {
            Log::error('PayNow Webhook: Order not found', [
                'payment_id' => $payment->id,
                'data' => $data
            ]);
            return null;
        }

        return $payNowPayment;
    }

    /**
     * Return an error response with the given message.
     *
     * @param string $message
     * @return array
     */
    private function returnErrorResponse(string $message): array
    {
        return [
            'status' => false,
            'message' => $message
        ];
    }
}
