<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Integrations\PayNow\Management
 */
class PaynowManagement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Integrations\PayNow\Management::class;
    }
}
