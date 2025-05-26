<?php

return [
    'enabled' => env('PAYNOW_ENABLED', false),
    'api_key' => env('PAYNOW_API_KEY', ''),
    'storefront_id' => env('PAYNOW_STORE_ID', ''),
    'tax_mode' => env('PAYNOW_TAX_MODE', 0), // 0: exclusive, 1: inclusive
];
