<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMerchantConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Merge to validate route parameter
        $this->merge(['merchant' => $this->route('merchant')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if ($this->has('enable')) {
            return ['enable' => 'boolean'];
        }

        return match ($this->merchant) {
            'paypal' => $this->paypalRules(),
            'paypalipn' => $this->paypalIPNRules(),
            'stripe' => $this->stripeRules(),
            'mollie' => $this->mollieRules(),
            'paytm' => $this->paytmRules(),
            'cashfree' => $this->cashfreeRules(),
            'mercadopago' => $this->mercadopagoRules(),
            'gopay' => $this->gopayRules(),
            'razorpay' => $this->razorpayRules(),
            'unitpay' => $this->unitpayRules(),
            'enot' => $this->enotRules(),
            'freekassa' => $this->freekassaRules(),
            'qiwi' => $this->qiwiRules(),
            'payu' => $this->payuRules(),
            'payuindia' => $this->payuIndiaRules(),
            'hotpay' => $this->hotpayRules(),
            'interkassa' => $this->interkassaRules(),
            default => ['merchant' => 'required'] // Add available merchant types
        };

    }

    /**
     * Validation rules for PayPal
     * @return string[]
     */
    private function paypalRules(): array
    {
        return [
            'test' => 'required',
            'paypal_user' => 'required',
            'paypal_password' => 'required',
            'paypal_signature' => 'required',
            'paypal_currency_code' => 'required'
        ];
    }

    /**
     * Validation rules for PayPalIPN
     * @return string[]
     */
    private function paypalIPNRules(): array
    {
        return [
            'test' => 'required',
            'paypal_business' => 'required',
            'paypal_currency_code' => 'required'
        ];
    }

    /**
     * Validation rules for Stripe
     * @return string[]
     */
    private function stripeRules(): array
    {
        return [
            'whsec' => 'required',
            'public' => 'required',
            'private' => 'required',
            'payment_methods' => 'required'
        ];
    }

    /**
     * Validation rules for Mollie
     * @return string[]
     */
    private function mollieRules(): array
    {
        return [
            'apiKey' => 'required',
        ];
    }

    /**
     * Validation rules for PayTM
     * @return string[]
     */
    private function paytmRules(): array
    {
        return [
            'test' => 'required',
            'mid' => 'required',
            'mkey' => 'required',
        ];
    }

    /**
     * Validation rules for CashFree
     * @return string[]
     */
    private function cashfreeRules(): array
    {
        return [
            'appId' => 'required',
            'secret' => 'required',
        ];
    }

    /**
     * Validation rules for MercadoPago
     * @return string[]
     */
    private function mercadopagoRules(): array
    {
        return [
            'test' => 'required',
            'token' => 'required',
            'currency' => 'required',
        ];
    }

    /**
     * Validation rules for GoPay
     * @return string[]
     */
    private function gopayRules(): array
    {
        return [
            'test' => 'required',
            'goid' => 'required',
            'ClientID' => 'required',
            'ClientSecret' => 'required',
        ];
    }

    /**
     * Validation rules for RazorPay
     * @return string[]
     */
    private function razorpayRules(): array
    {
        return [
            'test' => 'required',
            'api_key' => 'required',
            'api_secret' => 'required',
        ];
    }

    /**
     * Validation rules for UnitPay
     * @return string[]
     */
    private function unitpayRules(): array
    {
        return [
            'id' => 'required',
            'key' => 'required',
        ];
    }

    /**
     * Validation rules for Enot
     * @return string[]
     */
    private function enotRules(): array
    {
        return [
            'id' => 'required',
            'secret1' => 'required',
            'secret2' => 'required',
        ];
    }

    /**
     * Validation rules for FreeKassa
     * @return string[]
     */
    private function freekassaRules(): array
    {
        return [
            'id' => 'required',
            'secret' => 'required',
        ];
    }

    /**
     * Validation rules for Qiwi
     * @return string[]
     */
    private function qiwiRules(): array
    {
        return [
            'public_key' => 'required',
            'private_key' => 'required',
        ];
    }

    /**
     * Validation rules for PayU
     * @return string[]
     */
    private function payuRules(): array
    {
        return [
            'key' => 'required',
            'pos_id' => 'required',
            'currency' => 'required',
            'oauth_id' => 'required',
            'oauth_secret' => 'required'
        ];
    }

    /**
     * Validation rules for PayU India
     * @return string[]
     */
    private function payuIndiaRules(): array
    {
        return [
            'sandbox' => 'required',
            'key' => 'required',
            'salt' => 'required'
        ];
    }

    /**
     * Validation rules for HotPay
     * @return string[]
     */
    private function hotpayRules(): array
    {
        return [
            'sekret' => 'required',
        ];
    }

    /**
     * Validation rules for Interkassa
     * @return string[]
     */
    private function interkassaRules(): array
    {
        return [
            'cashbox_id' => 'required'
        ];
    }
}
