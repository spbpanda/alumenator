<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Models\Server;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServerSettingsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Adding a New Minecraft Server'));
        $this->loadSettings();
    }

    public function index(Request $r): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return redirect('/admin');
        }

        $servers = Server::query()->where('deleted', 0)->get();
        $defaultDelivery = $this->settings->withdraw_type;
        if (empty($defaultDelivery)) {
            $defaultDelivery = 'listener';
        }
        if ($defaultDelivery == 'plugin') {
            $defaultDelivery = 'websocket';
        }

        return view('admin.settings.servers', compact('servers', 'defaultDelivery'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return redirect('/admin');
        }

		$apiKey = Setting::select('api_secret')->first();
		$apiKey = json_decode($apiKey)->api_secret;

        return view('admin.settings.server', compact ('apiKey'));
    }
}
