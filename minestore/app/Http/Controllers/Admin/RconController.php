<?php

namespace App\Http\Controllers\Admin;

use App\Models\SecurityLog;
use App\Models\Server;
use App\PaymentLibs\Rcon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RconController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('RCON'));
    }

    public function index(Request $r)
    {
        if (!UsersController::hasRule('console', 'read')) {
            return redirect('/admin');
        }

        $servers = Server::query()->where('deleted', 0)->orderBy('id')->get();
        $serverId = $r->get('server');

        if (!$serverId) {
            $server = Server::query()->where('deleted', 0)->orderBy('id')->first();
        } else {
            $server = Server::query()->find($serverId);
        }

        if (!$server) {
            return redirect('/admin/settings/servers');
        }

        $rcon = new Rcon($server);

        if (@$rcon->connect()) {
            $firstOut = '<div class="console-out">['.Carbon::now().'] RCON connected!</div>';
            $enabled = true;
        } else {
            $firstOut = '<div class="console-out">['.Carbon::now().'] RCON error!</div>';
            $enabled = false;
        }

        return view('admin.rcon.index', compact('servers', 'server', 'firstOut', 'enabled'));
    }

    public function sendCommand(Request $r)
    {
        if (!UsersController::hasRule('console', 'write')) {
            return redirect('/admin');
        }

        $command = $r->get('command');
        $serverId = $r->get('server');

        $server = Server::query()->find($serverId);

        $rcon = new RCON($server);

        if (!@$rcon->connect()) {
            return 'RCON: Failed connection!';
        }

        $rcon->send_command($command);
        $text = $rcon->get_response();
        $rcon->disconnect();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['console'],
            'action_id' => $server->id,
        ]);

        return $text;
    }
}
