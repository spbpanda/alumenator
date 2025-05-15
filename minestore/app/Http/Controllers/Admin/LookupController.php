<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ban;
use App\Models\Payment;
use App\Models\User;
use App\Models\Whitelist;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LookupController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Lookup'));
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('lookup', 'read')) {
            return redirect('/admin');
        }

        return view('admin.lookup.index');
    }

    public function search($username, Request $r): View|RedirectResponse
    {
        if (!UsersController::hasRule('lookup', 'read')) {
            return redirect('/admin');
        }

        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', __('User not found in your webstore. Make sure the user has been detected on your webstore.'));
        }

        $uuid = null;
        $uuid_json = file_get_contents('https://minestorecms.com/api/uuid/name/' . $username);
        if ($uuid_json) {
            $uuid_temp = json_decode($uuid_json, true);
            $uuid = $uuid_temp['uuid'];
        }

        if ($uuid == 'undefined') {
            $uuid = null;
        }

        $history = [];

        // Check if user banned
        $ban = Ban::where('username', $username)
            ->when($uuid, fn($query) => $query->orWhere('uuid', $uuid))
            ->when($user->ip_address, fn($query) => $query->orWhere('ip', $user->ip_address))
            ->first();

        if ($ban) {
            $history[] = [
                'action' => 'ban',
                'ip' => $ban->ip ?? $user->ip_address ?? '',
                'reason' => $ban->reason ?? '',
                'date' => $ban->date,
            ];
        }

        // Check if user made a purchase on the store
        $payments = Payment::where('user_id', $user->id)
            ->whereIn('status', [Payment::PAID, Payment::COMPLETED, Payment::CHARGEBACK, Payment::ERROR])
            ->get();

        foreach ($payments as $payment) {
            $history[] = [
                'action' => 'purchase',
                'ip' => $payment->ip ?? $user->ip_address ?? '',
                'reason' => $payment->id,
                'date' => $payment->created_at,
            ];
        }

        // Check if user was whitelisted
        $whitelist = Whitelist::where('username', $username)
            ->orWhere('ip', $user->ip_address)
            ->first();

        if ($whitelist) {
            $history[] = [
                'action' => 'whitelist',
                'ip' => $whitelist->ip ?? $user->ip_address ?? '',
                'reason' => $whitelist->reason ?? '',
                'date' => $whitelist->date,
            ];
        }

        // Check if user has a chargeback history
        $chargebacks = Payment::where('user_id', $user->id)
            ->where('status', Payment::CHARGEBACK)
            ->get();

        foreach ($chargebacks as $chargeback) {
            $history[] = [
                'action' => 'chargeback',
                'ip' => $chargeback->ip ?? $user->ip_address ?? '',
                'reason' => $chargeback->id,
                'date' => $chargeback->created_at,
            ];
        }

        $resp = @file_get_contents("https://minestorecms.com/w/".config('app.LICENSE_KEY')."?nick=$username&extend=1", false, stream_context_create(['http' => ['timeout' => 20]]));
        $info = json_decode($resp, true);

        foreach ($info['history'] as $row) {
            $history[] = [
                'action' => 'ban',
                'ip' => isset($row['ip']) ? substr($row['ip'], 0, 3) . str_repeat('X', strlen($row['ip']) - 5) . substr($row['ip'], -2) : '',
                'reason' => 'Made a chargeback at another MineStoreCMS Minecraft Server Webstore',
                'date' => Carbon::parse($row['date']),
            ];
        }

        $rate = $info['total'] <= 0 ? 0 : ($info['back'] / $info['total'] * 100);

        return view('admin.lookup.user', compact('username', 'uuid', 'info', 'rate', 'ban', 'history'));
    }
}
