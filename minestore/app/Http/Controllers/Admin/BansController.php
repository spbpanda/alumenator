<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SortHelper;
use App\Http\Requests\BanUserRequest;
use App\Jobs\Utils;
use App\Models\Ban;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BansController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Bans'));
    }

    public function index(): View
    {
        if (!UsersController::hasRule('bans', 'read')) {
            return redirect('/admin');
        }

        return view('admin.bans.index');
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('bans', 'write')) {
            return redirect('/admin');
        }

        return view('admin.bans.create');
    }

    public function store(BanUserRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('bans', 'write')) {
            return redirect('/admin');
        }

        $ip = empty($request->input('ip')) ? null : $request->input('ip');

        $banQuery = [['username', $request->input('username')]];
        if (!is_null($ip)) {
            $banQuery[] = ['ip', $ip];
        }
        $ban = Ban::where($banQuery)->first();
        if (!empty($ban)) {
            return redirect('/admin/bans');
        }

        $reason = $request->input('reason') ? null : $request->input('ip');

        if (!is_null($reason)) {
            $banQuery[] = ['reason', $reason];
        }

        $reason = __('The reason for the has not been provided.');
        if ($request->input('reason') !== NULL) {
            $reason = $request->input('reason');
        }

        $uuid = null;
        $uuid_json = file_get_contents('https://minestorecms.com/api/uuid/name/' . $request->input('username'));
        if ($uuid_json) {
            $uuid_temp = json_decode($uuid_json, true);
            $uuid = $uuid_temp['uuid'];
        }

        $ban = Ban::create([
            'username' => $request->input('username'),
            'uuid' => $uuid,
            'ip' => is_null($ip) ? null : $ip,
            'reason' => $reason,
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['bans'],
            'action_id' => $ban->id,
        ]);

        Utils::dispatch([
            'method' => 'p',
            'ban' => 1,
            'nick' => $request->input('username'),
            'ip' => $ip,
        ]);

        return to_route('bans.index');
    }
}
