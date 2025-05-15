<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
                [
                    'name' => 'PayPal',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"test": "0", "paypal_user": "", "paypal_password": "", "paypal_signature": "", "paypal_currency_code": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PayPalIPN',
                    'enable' => '0',
					'can_subs' => '1',
					'config' => '{"test": "0", "paypal_business": "", "paypal_currency_code": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Cordarium',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"server_id": "", "public_key": "", "secret_key": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PayPal (Checkout)',
                    'enable' => '0',
                    'can_subs' => '1',
                    'config' => '{"client_id": "", "client_secret": "", "currency": "USD", "sandbox": "0", "payment_methods": ["card", "paypal"]}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'CoinPayments',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"currency_code": "", "secret_coinpayments": "", "merchant_coinpayments": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'G2APay',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"hash": "", "email": "", "secret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Stripe',
                    'enable' => '0',
					'can_subs' => '1',
					'config' => '{"whsec": "", "public": "", "private": "", "payment_methods": ["card"]}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Terminal3',
                    'enable' => '0',
					'can_subs' => '1',
					'config' => '{"public": "", "private": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Mollie',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"apiKey": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'CashFree',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"appId": "", "secret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'MercadoPago',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"test": "0", "token": "", "currency": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Paytm',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"mid": "", "mkey": "", "test": "0"}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'GoPay',
                    'enable' => '0',
					'can_subs' => '1',
					'config' => '{"goid": "", "test": "0", "ClientID": "", "ClientSecret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PayTR',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"merchant_id": "", "merchant_key": "", "merchant_salt": "", "currency": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'RazorPay',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"test": "0", "api_key": "", "api_secret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'UnitPay',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"id": "", "key": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'FreeKassa',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"id": "", "secret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Qiwi',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"public_key": "", "private_key": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Enot',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"id": "", "secret1": "", "secret2": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PayU',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"key": "", "pos_id": "", "currency": "", "oauth_id": "", "oauth_secret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'HotPay',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"sekret": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'InterKassa',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"cashbox_id": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Coinbase',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"api_key": "", "webhookSecret": "", "coinbase_currency": "USD"}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PayUIndia',
                    'enable' => '0',
					'can_subs' => '0',
					'config' => '{"key": "", "salt": "", "sandbox": "0"}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Skrill',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"email": "", "signature": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Coinpayments',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"currency": "", "secret": "", "merchant": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Fondy',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"currency": "", "merchant_id": "", "password": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Midtrans',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"serverKey": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'SePay',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"bank": "", "bank_account": "", "bank_owner": "", "paycode_prefix": "", "webhook_apikey": ""}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'PhonePe',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"merchant_id": "", "salt_key": "", "salt_index": "1"}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Virtual Currency',
                    'enable' => '0',
                    'can_subs' => '0',
                    'config' => '{"currency": "QQ"}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
		]);
    }
}
