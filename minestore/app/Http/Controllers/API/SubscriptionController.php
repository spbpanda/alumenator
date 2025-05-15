<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ManageSubscriptionRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    public function manageSubscription(ManageSubscriptionRequest $request, $api_key): \Illuminate\Http\JsonResponse
    {
        $settings = Setting::select('is_api', 'api_secret')->find(1);
        if ($settings->is_api != 1 || $settings->api_secret != $api_key) {
            return abort('403');
        }

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        $allowedStatuses = [
            Payment::PAID,
            Payment::COMPLETED,
        ];

        $payments = Payment::whereIn('status', $allowedStatuses)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($payments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No payments found.'
            ]);
        }

        foreach ($payments as $payment) {
            $subscription = Subscription::where('payment_id', $payment->id)
                ->where('status', Subscription::ACTIVE)
                ->first();

            if ($subscription) {
                $url[] = match ($subscription->payment_method) {
                    'stripe' => $this->getPortalLink($subscription->customer_id),
                    'gopay' => 'GoPay is not supported to close subscription directly. Contact Staff Team.',
                    'paypalipn' => 'PayPal is not supported to close subscription directly. Contact Staff Team.',
                    'terminal3' => 'Terminal3 is not supported to close subscription directly. Contact Staff Team.',
                    default => 'Unknown payment method.',
                };
            }
        }

        if (!isset($url)) {
            return response()->json([
                'success' => false,
                'message' => 'No subscriptions found.',
                'url' => ''
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions found.',
            'urls' => $url,
        ]);
    }

    public function getPortalLink($customer_id): string|null
    {
        $paymentMethod = PaymentMethod::query()->where('name', 'Stripe')->first();
        if (!$paymentMethod->enable) {
            return null;
        }

        $config = json_decode($paymentMethod->config, true);

        $stripe = new StripeClient($config['private']);

        try {
            $session = $stripe->billingPortal->sessions->create([
                'customer' => $customer_id,
                'return_url' => config('app.url'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating session: ' . $e->getMessage());
            return 'Error creating session. Please try again.';
        }

        return $session->url;
    }
}
