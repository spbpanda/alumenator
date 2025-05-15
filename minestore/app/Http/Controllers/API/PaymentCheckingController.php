<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CheckPaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class PaymentCheckingController extends Controller
{
    public function check(CheckPaymentRequest $request): JsonResponse
    {
        $ipAddress = $request->ip();
        $payment_data = [];
        $key = "checkPaymentRequest:$ipAddress";

        RateLimiter::attempt(
            $key,
            $perMinute = 18,
            function () {
                return true;
            }
        );

        if (RateLimiter::tooManyAttempts($key, $perMinute)) {
            return $this->message('forbidden', 'You are not allowed to check this payment.', $payment_data);
        }

        $payment = Payment::where('internal_id', $request->order_id)
            ->select(['id', 'user_id', 'internal_id', 'price', 'currency', 'status', 'gateway', 'created_at'])
            ->first();

        if (!$payment) {
            return $this->message('not_found', 'Payment not found.', $payment_data);
        }

        if ($payment->user_id !== auth()->id()) {
            return $this->message('forbidden', 'You are not allowed to check this payment.', $payment_data);
        }

        $payment->created_at = $payment->created_at->format('Y-m-d H:i:s');
        $payment_data = $payment->toArray();
        unset($payment_data['user_id']);
        unset($payment_data['user']);

        return match ($payment->status) {
            Payment::PROCESSED => $this->message('pending', 'Payment is pending.', $payment_data),
            Payment::PAID, Payment::COMPLETED => $this->message('success', 'Payment is successful.', $payment_data),
            Payment::ERROR => $this->message('failed', 'Payment has failed.', $payment_data),
            default => $this->message('not_found', 'Unknown status.', $payment_data),
        };
    }

    private function message(string $status, string $message, array $payment): JsonResponse
    {
        return response()->json([ 'status' => $status, 'message' => $message , 'order_data' => $payment]);
    }
}
