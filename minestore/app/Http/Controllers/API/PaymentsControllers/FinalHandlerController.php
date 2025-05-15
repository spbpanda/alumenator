<?php

namespace App\Http\Controllers\API\PaymentsControllers;

use App\Events\PaymentPaid;
use App\Http\Controllers\Admin\DonationGoalsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ItemsController;
use App\Jobs\SendEmail;
use App\Models\CartItem;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Crypt;
use Illuminate\Support\Facades\Log;

class FinalHandlerController extends Controller
{
    public function finalHandler($paymentId)
    {
        $payment = Payment::query()->with(['user'])->where([['id', $paymentId], ['status', Payment::PROCESSED]])->first();
        if (! $payment) {
            exit('Unable to find the payment!');
        }

        $cart = $payment->cart()->first();

        DonationGoalsController::increment($cart->price);

        $this->EmailOrderNotification($paymentId);

        $result = ItemsController::giveItems($payment);

        if ($result) {
            $payment->update([
                'status' => Payment::PAID,
            ]);
            event(new PaymentPaid($payment));
        } else {
            $payment->update([
                'status' => Payment::ERROR,
            ]);
        }

        $usdPrice = $payment->price;
        if ($payment->currency != 'USD') {
            $usd_currency = Currency::query()->where('name', 'USD')->first();
            $currencyRate = Currency::query()->where('name', $payment->currency)->first();
            $usdPrice = round($this->toActualCurrency($payment->price, $currencyRate->value, $usd_currency->value), 2);
        }

        $nick = User::where('id', $payment->user_id)->first()->username;
        $ip = '';
        if (! empty($payment->ip)) {
            $ip = '&ip='.$payment->ip;
        }

        @file_get_contents("https://minestorecms.com/p/".config('app.LICENSE_KEY')."?nick=$nick&id=".$payment->id.$ip.'&amount='.$usdPrice, false, stream_context_create(['https' => ['timeout' => 6]]));

        return true;
    }

    public function EmailOrderNotification($paymentId)
    {
        // Check if email sending is enabled
        $settings = Setting::find(1);
        if (!$settings->smtp_enable) {
            return false;
        }

        // Fetch necessary data
        $payment = Payment::where('id', $paymentId)->first();
        if (!$payment) {
            return false; // Handle missing payment
        }

        $items = [];
        $total = $payment->price;
        $username = $payment->user->username;
        $cartItems = CartItem::where('cart_id', $payment->cart->id)->get();
        foreach ($cartItems as $cartItem) {
            $items[] = [
                'id' => $cartItem->item->id,
                'name' => $cartItem->item->name,
                'qty' => $cartItem->count,
                'price' => $cartItem->count * $cartItem->item->price,
                'currency' => $payment->currency, // Use payment currency
            ];
        }

        // Extract email from payment details (handle errors)
        $email = "";
        if (!empty($payment->details)) {
            $details = json_decode($payment->details, true);
            if (isset($details['email'])) {
                $email = $details['email'];
            }
        }
        if (empty($email)) {
            Log::error('Mail Error Email Order Notification: ' . json_encode([$cartItem, $payment]));
            return false;
        }

        // Decrypt SMTP password
        $smtp_password = Crypt::decryptString($settings->smtp_pass);

        SendEmail::dispatch([
            'settings' => [
                'Host' => $settings->smtp_host,
                'SMTPAuth' => true,
                'Username' => $settings->smtp_user,
                'Password' => $smtp_password,
                //'SMTPSecure' => 'tls',
                'Port' => $settings->smtp_port,
                'setFrom' => [$settings->smtp_from, $settings->site_name],
                'addAddress' => $email,
                'subject' => __('Thank you for your purchase!'),
            ],
            'email' => $email,
            'template' => 'emails.order',
            'fields' => [
                'site_name' => $settings->site_name,
                'payment' => $payment,
                'items' => $items,
                'total' => $total,
                'username' => $username,
            ],
        ]);

        return true;
    }
}
