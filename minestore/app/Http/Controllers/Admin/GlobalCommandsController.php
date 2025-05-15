<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GlobalCommandsSaveRequest;
use App\Models\GlobalCommand;
use App\Models\ItemServer;
use App\Models\SecurityLog;
use App\Models\Server;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GlobalCommandsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Global Commands'));
        $this->loadSettings();
    }

    public function index()
    {
        if (!UsersController::hasRule('global_commands', 'read')) {
            return redirect('/admin');
        }

        $enable_globalcmd = $this->settings->enable_globalcmd;
        $currency = $this->settings->currency;
        $cmds = GlobalCommand::all();
        $servers = Server::where('deleted', 0)->get();

        return view('admin.globalcommands.index', compact('enable_globalcmd', 'currency', 'cmds', 'servers'));
    }

    public function save(GlobalCommandsSaveRequest $r)
    {
        if (!UsersController::hasRule('global_commands', 'write')) {
            return redirect('/admin');
        }

        $commands = [];
        if ($r->input('command'))
        {
            foreach ($r->input('command') as $key => $value)
            {
                $commands[] = $value;
            }
        }

        foreach (GlobalCommand::query()->get() as $command) {
            $i = 0;
            $isGlobalCommandExist = false;
            for ( ; $i < count($commands); $i++) {
                if ($commands[$i]['id'] == $command->id)
                    $isGlobalCommandExist = true;
                    break;
            }

            if ($isGlobalCommandExist) {
                $servers = [];
                if (!empty($commands[$i]['servers']) && is_array($commands[$i]['servers']) && !in_array('ALL', $commands[$i]['servers'])) {
                    $servers = array_values($commands[$i]['servers']);
                }

                ItemServer::where([['type', ItemServer::TYPE_GLOBAL_COMMAND_SERVER], ['item_id', $command->id]])->delete();
                foreach ($servers as $server){
                    if (empty($server) || $server == 'ALL')
                        continue;

                    ItemServer::create([
                        'type' => ItemServer::TYPE_GLOBAL_COMMAND_SERVER,
                        'item_id' => $command->id,
                        'server_id' => $server,
                    ]);
                }

                if (isset($commands[$i]['is_online']) && $commands[$i]['is_online'][0] == 'on')
                    $commands[$i]['is_online'] = 1;
                else
                    $commands[$i]['is_online'] = 0;

                $command->update($commands[$i]);

                unset($commands[$i]);
                $commands = array_values($commands);

                SecurityLog::create([
                    'admin_id' => \Auth::guard('admins')->user()->id,
                    'method' => SecurityLog::UPDATE_METHOD,
                    'action' => SecurityLog::ACTION['global_commands'],
                    'action_id' => $command->id,
                ]);
            } else {
                ItemServer::where([['type', ItemServer::TYPE_GLOBAL_COMMAND_SERVER], ['item_id', $command->id]])->delete();
                $command->delete();
                SecurityLog::create([
                    'admin_id' => \Auth::guard('admins')->user()->id,
                    'method' => SecurityLog::DELETE_METHOD,
                    'action' => SecurityLog::ACTION['global_commands'],
                    'action_id' => $command->id,
                ]);
            }
        }

        foreach ($commands as $command) {
            $globalCommand = GlobalCommand::create([
                'cmd' => $command['cmd'],
                'is_online' => isset($command['is_online']) && $command['is_online'][0] == 'on' ? 1 : 0,
                'price' => str_replace(',', '.', $command['price']),
            ]);

            $servers = [];
            if (!empty($command['servers']) && is_array($command['servers']) && !in_array('ALL', $command['servers'])){
                foreach ($command['servers'] as $server) {
                    $servers[] = is_array($server) ? $server[0] : $server;
                }
            }

            foreach ($servers as $server){
                if (empty($server) || $server == 'ALL')
                    continue;

                ItemServer::create([
                    'type' => ItemServer::TYPE_GLOBAL_COMMAND_SERVER,
                    'item_id' => $globalCommand->id,
                    'server_id' => $server,
                ]);
            }

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::CREATE_METHOD,
                'action' => SecurityLog::ACTION['global_commands'],
                'action_id' => $globalCommand->id,
            ]);
        }

        Setting::query()->find(1)->update([
            'enable_globalcmd' => $r->input('enable_globalcmd') == 'on' ? 1 : 0,
        ]);

        return redirect('/admin/globalCommands');
    }

    public function delete($id)
    {
        if (!UsersController::hasRule('global_commands', 'del')) {
            return redirect('/admin');
        }

        GlobalCommand::where('id', $id)->delete();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['global_commands'],
            'action_id' => $id,
        ]);

        return redirect('/admin/global_commands');
    }
}
