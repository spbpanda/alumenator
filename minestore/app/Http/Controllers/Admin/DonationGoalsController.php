<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ItemsController;
use App\Models\CommandHistory;
use App\Models\ItemServer;
use App\Models\Payment;
use App\Models\SecurityLog;
use App\Models\Server;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\DonationGoal;
use Illuminate\Http\Request;

class DonationGoalsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Donation Goals'));
        $this->loadSettings();
    }

    /**
     * Page with all donation goals
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('donation_goals', 'read')) {
            return redirect('/admin');
        }
        $donation_goals = DonationGoal::query()->orderBy('id')->paginate(30);
        return view('admin.donation_goals.index', compact('donation_goals'));
    }

    /**
     * Page with create form
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('donation_goals', 'write')) {
            return redirect('/admin');
        }
        $isExist = false;
        $servers = Server::where('deleted', 0)->get();
        return view('admin.donation_goals.donationGoal', compact('isExist', 'servers'));
    }

    public function store(Request $r): RedirectResponse
    {
        if (!UsersController::hasRule('discounts', 'write')) {
            return redirect('/admin');
        }

        $r->validate($this->getValidationRules());

        $commands = $this->processCommands($r);
        $servers = $this->processServers($r);
        $startAt = $this->formatDate($r->input('start_at'));
        $disableAt = $this->formatDate($r->input('disable_at'));
        $isEnabled = $startAt == null || Carbon::now() > $startAt ? 1 : 0;

        $donationGoal = DonationGoal::create([
            'name' => $r->input('name'),
            'status' => $this->getStatus($r->input('status')),
            'is_enabled' => $isEnabled,
            'automatic_disabling' => $this->getStatus($r->input('automatic_disabling')),
            'automatic_reset' => $this->getStatus($r->input('automatic_reset')),
            'current_amount' => $this->formatAmount($r->input('current_amount')),
            'goal_amount' => $this->formatAmount($r->input('goal_amount')),
            'cmdExecute' => $this->getStatus($r->input('cmdExecute')),
            'commands_to_execute' => json_encode($commands),
            'servers' => json_encode($servers),
            'start_at' => $startAt,
            'disable_at' => $disableAt
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['donation_goal'],
            'action_id' => $donationGoal->id,
        ]);

        return to_route('donation_goals.index');
    }

    public function show($id): \Illuminate\View\View|RedirectResponse
    {
        if (!UsersController::hasRule('donation_goals', 'read')) {
            return redirect('/admin');
        }

        $isExist = true;
        $donationGoal = DonationGoal::find($id);
        $servers = Server::where('deleted', 0)->get();

        return view('admin.donation_goals.donationGoal', compact('isExist', 'donationGoal', 'servers'));
    }

    public function update(Request $r, $id): RedirectResponse
    {
        if (!UsersController::hasRule('donation_goals', 'write')) {
            return redirect('/admin');
        }

        $r->validate($this->getValidationRules());

        $commands = $this->processCommands($r);
        $servers = $this->processServers($r);
        $startAt = $this->formatDate($r->input('start_at'));
        $disableAt = $this->formatDate($r->input('disable_at'));
        $isEnabled = $startAt == null || Carbon::now() > $startAt ? 1 : 0;

        DonationGoal::find($id)->update([
            'name' => $r->input('name'),
            'status' => $this->getStatus($r->input('status')),
            'is_enabled' => $isEnabled,
            'automatic_disabling' => $this->getStatus($r->input('automatic_disabling')),
            'automatic_reset' => $this->getStatus($r->input('automatic_reset')),
            'current_amount' => $this->formatAmount($r->input('current_amount')),
            'goal_amount' => $this->formatAmount($r->input('goal_amount')),
            'cmdExecute' => $this->getStatus($r->input('cmdExecute')),
            'commands_to_execute' => json_encode($commands),
            'servers' => json_encode($servers),
            'start_at' => $startAt,
            'disable_at' => $disableAt
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['donation_goal'],
            'action_id' => $id,
        ]);

        return to_route('donation_goals.index');
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('donation_goals', 'del')) {
            return redirect('/admin');
        }

        try {
            DonationGoal::query()->where('id', $id)->delete();
            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['donation_goal'],
                'action_id' => $id,
            ]);
        } catch (\Exception $e) {
        }

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('donation_goals.index');
    }

    public static function increment($price)
    {
        DonationGoal::where('status', 1)->increment('current_amount', $price);
        $goals = DonationGoal::whereRaw('status = 1 AND cmdExecute = 1 AND reached_at IS NULL AND current_amount >= goal_amount')->get();
        if (!$goals->isEmpty()) {
            foreach ($goals as $goal) {
                self::sendCommand($goal);
                $goal->reached_at = Carbon::now();
                $goal->cmdExecute = 1;
                if ($goal->automatic_disabling === 1) {
                    $goal->status = 0;
                }

                if ($goal->automatic_reset === 1) {
                    $goal->status = 1;
                    $goal->current_amount = 0;
                    $goal->reached_at = null;
                }

                $goal->save();
            }
        }

        DonationGoal::whereRaw('status = 1 AND cmdExecute = 0 AND automatic_disabling = 0 AND reached_at IS NULL AND current_amount >= goal_amount')
            ->update(['reached_at' => Carbon::now()]);
        DonationGoal::whereRaw('status = 1 AND cmdExecute = 0 AND automatic_disabling = 1 AND current_amount >= goal_amount')
            ->update(['reached_at' => Carbon::now(), 'status' => 0]);
    }

    private function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'string|max:3',
            'automatic_disabling' => 'string|max:3',
            'automatic_reset' => 'sometimes|string|max:3',
            'current_amount' => 'required',
            'goal_amount' => 'required',
            'cmdExecute' => 'string|max:3',
            'commands' => 'sometimes|array',
            'servers' => 'sometimes|array',
            'start_at' => 'nullable|date',
            'disable_at' => 'nullable|date',
        ];
    }

    private function processCommands(Request $r): array
    {
        $commands = [];
        if ($r->has('commands') && !empty($r->input('commands'))) {
            foreach ($r->input('commands') as $cmd) {
                $commands[] = $cmd['cmd'];
            }
        }
        return $commands;
    }

    private function processServers(Request $r): array
    {
        $servers = [];
        if ($r->has('servers') && !empty($r->input('servers'))) {
            foreach ($r->input('servers') as $server) {
                $servers[] = $server;
            }
        }
        return $servers;
    }

    private function formatDate(?string $date): ?string
    {
        return $date ? Carbon::createFromFormat('Y-m-d H:i', $date)->format('Y-m-d H:i:00') : null;
    }

    private function getStatus(?string $status): int
    {
        return $status === 'on' ? 1 : 0;
    }

    private function formatAmount(?string $amount): string
    {
        return str_replace(',', '.', $amount);
    }

    public static function sendCommand($goal) {
        $setting = Setting::take(1)->first();
        $servers = [];
        $serversArray = json_decode($goal->servers, true);
        if (in_array('ALL', $serversArray)){
            $servers = Server::where('deleted', 0)->get();
        } else {
            $servers = Server::whereIn('id', $serversArray)->where('deleted', 0)->get();
        }

        foreach ($servers as $server) {
            if ($setting->withdraw_game == 'minecraft') {
                $sourceCommands = json_decode($goal->commands_to_execute, true);

                for ($i = 0; $i < count($sourceCommands); $i++) {
                    $cmd = str_replace('{time}', Carbon::now()->format('H:i:s'), $sourceCommands[$i]);
                    $cmd = str_replace('{date}', Carbon::now()->format('Y-m-d'), $cmd);
                    $cmd = str_replace('{goal}', $goal->name, $cmd);
                    $cmd = str_replace('{current_amount}', $goal->current_amount, $cmd);
                    $cmd = str_replace('{goal_amount}', $goal->goal_amount, $cmd);
                    $cmd = str_replace('{reached_at}', $goal->reached_at, $cmd);
                    $cmd = str_replace('{automatic_disabling}', $goal->automatic_disabling, $cmd);
                    $cmdHistory = CommandHistory::create([
                        'type' => CommandHistory::TYPE_DONATION_GOAL,
                        'cmd' => $cmd,
                        'username' => '',
                        'server_id' => $server->id,
                        'status' => CommandHistory::STATUS_QUEUE,
                        'is_online_required' => false,
                    ]);
                    if ($server->method === 'websocket') {
                        ItemsController::sendWebsocket($cmd, $server, '', false);
                    } elseif ($server->method === 'rcon') {
                        if (ItemsController::sendRcon($cmd, $server))
                            $cmdHistory->update(['status', CommandHistory::STATUS_EXECUTED]);
                    } elseif ($server->method === 'listener') {
                        ItemsController::sendListener($cmd, '', false, $cmdHistory->id, null);
                    }
                }
            }
        }
    }
}
