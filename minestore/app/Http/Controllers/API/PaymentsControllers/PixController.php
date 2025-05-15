<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Request;

class PixController extends Controller
{
    public static function create($cart, $payment, $currency): ?array
    {
        try {
            $paymentMethod = PaymentMethod::query()->where('name', 'MercadoPago')->first();
            if (!$paymentMethod->enable) return null;

            $config = json_decode($paymentMethod->config, true);

            if ($currency != "BRL") {
                $system_currency = Currency::query()->where("name", Setting::query()->select('currency')->find(1)->currency)->first();
                $currencyRate = Currency::query()->where("name", "BRL")->first();
                $payment->price = round(Controller::toActualCurrency($cart->price, $currencyRate->value, $system_currency->value), 2);
                $payment->update([
                    'price' => round($payment->price),
                    'currency' => "BRL"
                ]);
            }

            $cart->update([
                'is_active' => 0
            ]);

            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-IDEMPOTENCY-KEY: ' . uniqid(),
                'Authorization: Bearer ' . $config['token']
            ];

            $details = json_decode($payment->details, true);
            $first_name = explode(' ', $details['fullname'])[0];
            $last_name = explode(' ', $details['fullname'])[1] ?? $first_name;

            $fields = [
                "installments" => 1,
                "transaction_amount" => round($payment->price, 2),
                "description" => "Purchasing a digital item for Minecraft Server.",
                "payment_method_id" => "pix",
                "external_reference" => $payment->id,
                "notification_url" => 'https://' . request()->getHost() . '/api/payments/handle/mercadopago',
                "payer" => [
                    "email" => $details['email'],
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/v1/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);

            if (empty($response)) return null;
            $response = json_decode($response, true);

            // Extract QR code and PIX code
            $qrCodeBase64 = $response['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
            $qrCode = $response['point_of_interaction']['transaction_data']['qr_code'] ?? null;

            return [
                'type' => 'qrcode',
                'details' => [
                    'payment_method' => 'pix',
                    'payment_id' => $payment->internal_id,
                    'payment_price' => round($payment->price) . ' ' . $payment->currency,
                    'qr_code_base64' => $qrCodeBase64,
                    'qrcode' => $qrCode,
                ]
            ];
        } catch (\Throwable $e) {
            \Log::error('Error in payment creation: ' . $e->getMessage(), [
                'cart' => $cart,
                'payment' => $payment,
                'currency' => $currency,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while processing the payment. Please try again later.' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check the status of a payment.
     *
     * @param Request $request The incoming HTTP request containing the payment order ID.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the status of the payment.
     */
    public function check(Request $request)
    {
        $data = $request->post();

        if (empty($data['order_id'])) {
            return response()->json([
                'success' => false,
                'error' => 'Nenhum dado para verificar.'
            ], 403);
        }

        $internal_payment_id = $data['order_id'];
        $payment = Payment::where('internal_id', $internal_payment_id)->first();

        // Verify if the payment exists
        if (!$payment) {
            return response()->json([
                'success' => false,
                'error' => 'Transação não encontrada.'
            ], 404);
        }

        // Check if the payment is pending
        return match ($payment->status) {
            Payment::PAID => response()->json([
                'success' => true,
                'message' => 'Pagamento aprovado para o ID: ' . $internal_payment_id,
                'status' => 'approved',
            ], 200),
            Payment::PROCESSED => response()->json([
                'success' => false,
                'message' => 'Pagamento pendente para o ID: ' . $internal_payment_id,
                'status' => 'pending',
            ], 200),
            default => response()->json([
                'success' => false,
                'message' => 'Status do pagamento desconhecido para o ID: ' . $internal_payment_id,
                'status' => 'unknown',
            ], 400),
        };
    }
}
