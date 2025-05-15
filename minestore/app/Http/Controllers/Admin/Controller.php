<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $settings = null;

    public function loadSettings()
    {
        if (!config('app.is_installed')) return;

        $this->settings = Setting::find(1);
        view()->share('settings', $this->settings);
    }

    public function setTitle($title)
    {
        view()->share('title', $title);
    }

    public function toActualCurrency($price, $currency_value, $system_currency_value)
    {
        return round(($price * $currency_value) / $system_currency_value, 2);
    }
}
