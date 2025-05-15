<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreWhitelistRequest;
use App\Models\Whitelist;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WhitelistController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Whitelist'));
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('bans', 'read')) {
            return redirect('/admin');
        }

        return view('admin.whitelist.index');
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('bans', 'write')) {
            return redirect('/admin');
        }

        return view('admin.whitelist.create');
    }

    public function store(StoreWhitelistRequest $request): RedirectResponse
    {
        $this->setTitle('Whitelist the User');
        if (!UsersController::hasRule('bans', 'write')) {
            return redirect('/admin');
        }

        $ip = empty($request->input('ip')) ? null : $request->input('ip');

        $whitelistQuery = [['username', $request->input('username')]];
        if (!is_null($ip)) {
            $whitelistQuery[] = ['ip', $ip];
        }
        $whitelist = Whitelist::where($whitelistQuery)->first();
        if (!empty($whitelist)) {
            return redirect('/admin/whitelist');
        }

        $reason = $request->input('reason') ? null : $request->input('ip');

        if(!is_null($reason)){
            $whitelistQuery[] = ['reason', $reason];
        }

        if($request->input('reason') !== NULL){
            $reason = $request->input('reason');
        } else {
            $reason = __('The reason for the whitelist record is not provided.');
        }

        $whitelist = Whitelist::create([
            'username' => $request->input('username'),
            'ip' => is_null($ip) ? null : $ip,
            'reason' => $reason,
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['whitelist'],
            'action_id' => $whitelist->id,
        ]);

        return to_route('whitelist.index');
    }
}
