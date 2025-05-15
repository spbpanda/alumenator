<?php

namespace App\Http\Controllers\Admin;

use App\Models\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use OTPHP\TOTP;
use App\Models\Admin;

class AdminProfileController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Admin Profile'));
        $this->loadSettings();
    }

    public function index(): View
    {
        $otp = null;
        if(!\Auth::guard('admins')->user()->is_2fa){
            $otp = TOTP::generate()->getSecret();
            Session::put('otp', $otp);
        }

        return view('admin.profile.index', compact('otp'));
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'user' => 'required',
            'currentPassword' => 'required',
            'newPassword' => 'nullable|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,60}$/',
            'confirmPassword' => 'nullable|same:newPassword',
        ]);

        $user = Admin::where('username', $request->user)->first();

        if (!is_null($data['currentPassword'])) {
            if (Hash::check($data['currentPassword'], $user->password)) {
                if (!is_null($data['newPassword'])) {
                    $this->updatePassword($user, $data);
                    return redirect('/admin/profile')->with('success', __('Password was changed successfully!'));
                }
                return redirect('/admin/profile')->with('status', __('There is nothing to change!'));
            } else {
                return redirect('/admin/profile')->with('error', __('Current password is not correct!'));
            }
        } else {
            return redirect('/admin/profile')->with('status', __('There is nothing to change!'));
        }
    }

    private function updatePassword($user, $data)
    {
        $user->update([
            'password' => Hash::make($data['newPassword']),
            'last_login_time' => now(),
        ]);
    }

    public function store(Request $r)
    {
        if ($r->has('disable2fa')){
            Admin::where('id', \Auth::guard('admins')->user()->id)->update([
                'is_2fa' => 0,
            ]);
        } else {
            if(!$r->has('currentPassword') || !$r->has('newPassword') || !$r->has('confirmPassword'))
                return;

            if (\Auth::guard('admins')->user()->password != hash('sha256', $r->input('currentPassword')))
                return;

            if ($r->input('newPassword') != $r->input('confirmPassword'))
                return;

            Admin::query()->where('id', \Auth::guard('admins')->user()->id)->update(['password' => hash('sha256', $r->input('newPassword'))]);
        }

        return redirect('/admin/profile');
    }

    public function setOTP($code){
        $secret = Session::get('otp');
        $otp = TOTP::createFromSecret($secret)->now();
        if ($otp == $code){
            Admin::where('id', \Auth::guard('admins')->user()->id)->update([
                'is_2fa' => 1,
                'totp' => $secret,
            ]);
            Session::forget('otp');
            return 'OK';
        }
        return 'NO';
    }
}
