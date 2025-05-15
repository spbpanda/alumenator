<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\SecurityLog;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OTPHP\TOTP;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

    public function login(Request $r): string
    {
        $username = $r->input('login');
        $password = $r->input('password');

        $admin = Admin::query()->where('username', $username)->first();
        if (!$admin || !Hash::check($password, $admin->password)) {
            return 'FAIL';
        }

        if ($admin->is_2fa == 1) {
            $r->session()->put('try_login', $admin->id);
            return '2FA';
        }

        Auth::guard('admins')->login($admin, true);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['login'],
            'action_id' => $admin->id,
        ]);

        return 'OK';
    }

    public function verify2fa(Request $r): string
    {
        if (!$r->session()->has('try_login'))
            return 'FAIL';

        $admin = Admin::query()->where('id', $r->session()->get('try_login'))->first();

        $otp = TOTP::createFromSecret($admin->totp)->now();
        if ($r->input('otp') != $otp)
            return 'FAIL';

        Auth::guard('admins')->login($admin, true);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['login'],
            'action_id' => $admin->id,
        ]);

        return 'OK';
    }

    public function logout()
    {
        Auth::guard('admins')->logout();

        return redirect('/admin/login');
    }
}
