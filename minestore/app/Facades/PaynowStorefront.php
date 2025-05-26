<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Integrations\PayNow\Storefront
 */
class PaynowStorefront extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Integrations\PayNow\Storefront::class;
    }
}
