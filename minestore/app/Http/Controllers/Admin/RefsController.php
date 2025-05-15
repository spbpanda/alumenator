<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommandHelper;
use App\Http\Requests\StoreRefRequest;
use App\Http\Requests\UpdateRefRequest;
use App\Models\Command;
use App\Models\ItemServer;
use App\Models\RefCode;
use App\Models\SecurityLog;
use App\Models\Server;
use App\Models\Variable;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RefsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Player Referrals'));
    }

    public function index(): View|RedirectResponse
    {
        if (!UsersController::hasRule('referers', 'read')) {
            return redirect('/admin');
        }

		$isRefEnabled = Setting::query()->find(1)->is_ref;

        return view('admin.refs.index', compact('isRefEnabled'));
    }

    public function create(): View|RedirectResponse
    {
        if (!UsersController::hasRule('referers', 'write')) {
            return redirect('/admin');
        }

        $servers = Server::query()->where('deleted', 0)->get();
        $vars = Variable::query()->where('deleted', 0)->get();
        $isExist = false;

        return view('admin.refs.ref', compact('isExist', 'servers', 'vars'));
    }

    public function store(StoreRefRequest $request): RedirectResponse
    {
        if (!UsersController::hasRule('referers', 'write')) {
            return redirect('/admin');
        }

        $data = $request->validated();

        $commands = [];
        $servers = [];
        $is_server_choice = 0;
        if ($request->has('cmd') && $request->input('cmd') == 'on' && $request->has('minecraft')) {
            foreach ($request->input('minecraft') as $command) {
                $is_server_choice = $is_server_choice === 1 || (isset($command['is_server_choice']) && $command['is_server_choice'] == 'on') ? 1 : 0;
                if (isset($command['commands'])) {
                    foreach ($command['commands'] as $subCommand)
                    {
                        $commands[] = $subCommand;
                    }
                }
                if (isset($command['servers'])){
                    
                    foreach ($command['servers'] as $subServer)
                    {
                        $servers[] = intval($subServer);
                    }
                }
            }
        }
        $servers = array_unique($servers);
        foreach ($servers as $commandServer){
            if (empty($commandServer) || $commandServer == 'ALL'){
                $servers = [];
                break;
            }
        }
        /*for ($i = 0; $i < count($commands); $i++){
            $commands[$i]['servers'] = $servers;
        }*/
        
        $data['cmd'] = $request->input('cmd');
        $data['commands'] = $commands;
        $ref = RefCode::create($data);

        $commandsToDelete = Command::where('item_type', Command::REF_COMMAND)->where('item_id', $ref->id)->select('id')->get()->pluck('id')->toArray();
        if (!empty($commandsToDelete)){
            $deletePlaceholder = implode(",", array_fill(0, count($commandsToDelete), '?'));
            DB::delete('DELETE item_servers FROM item_servers JOIN commands ON commands.id = item_servers.item_id WHERE item_servers.type = '.ItemServer::TYPE_REF_COMMAND_SERVER.' AND commands.item_id IN ('.$deletePlaceholder.')', $commandsToDelete);
        }

        Command::where('item_type', Command::REF_COMMAND)->where('item_id', $ref->id)->delete();

        for ($i = 0; $i < count($commands); $i++){            
            $delayValue = intval($commands[$i]['delay_value']);
            $delayUnit = intval($commands[$i]['delay_unit']);
            if ($delayValue > 0)
                $delayValue = CommandHelper::GetDelayValueSeconds($delayUnit, $delayValue);

            $repeatValue = intval($commands[$i]['repeat_value']);
            $repeatUnit = intval($commands[$i]['repeat_unit']);
            if ($repeatValue > 0)
                $repeatValue = CommandHelper::GetDelayValueSeconds($repeatUnit, $repeatValue);

            $command = Command::create([
                'item_type' => Command::REF_COMMAND,
                'item_id' => $ref->id,
                'command' => $commands[$i]['command'],
                'event' => $commands[$i]['event'],
                'is_online_required' => $commands[$i]['is_online_required'] ?? 0,
                'delay_value' => $delayValue,
                'delay_unit' => $delayUnit,
                'repeat_unit' => $repeatUnit,
                'repeat_value' => $repeatValue,
                'repeat_cycles' => $commands[$i]['repeat_cycles'] ?? 0,
            ]);
            
            if (!empty($commands[$i]['servers'])){
                $isSpecificServers = true;
                foreach ($commands[$i]['servers'] as $commandServer) {
                    if (empty($commandServer) || $commandServer == 'ALL')
                        $isSpecificServers = false;
                }

                if ($isSpecificServers){
                    foreach ($commands[$i]['servers'] as $commandServer) {
                        if (empty($commandServer) || $commandServer == 'ALL')
                            continue;
                        
                        ItemServer::create([
                            'type' => ItemServer::TYPE_REF_COMMAND_SERVER,
                            'item_id' => $ref->id,
                            'server_id' => $commandServer,
                            'cmd_id' => $command->id,
                        ]);
                    }
                }
            }
        }

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['referers'],
            'action_id' => $ref->id,
        ]);

        return to_route('refs.index');
    }


    public function show(string $id): View|RedirectResponse
    {
        if (!UsersController::hasRule('referers', 'read')) {
            return redirect('/admin');
        }

        $servers = Server::query()->where('deleted', 0)->get();
        $ref = RefCode::find($id);
        if (!$ref)
            return redirect()->route('refs.index');
        if ($ref->deleted == 1)
            return redirect()->route('refs.index');

        $isExist = true;

        return view('admin.refs.ref', compact('isExist', 'ref', 'servers'));
    }

    public function update(UpdateRefRequest $request, string $id): RedirectResponse
    {
        if (!UsersController::hasRule('referers', 'write')) {
            return redirect('/admin');
        }

        $data = $request->validated();

        $commands = [];
        $servers = [];
        $is_server_choice = 0;
        if ($request->has('cmd') && $request->input('cmd') == 'on' && $request->has('minecraft')) {
            foreach ($request->input('minecraft') as $command) {
                $is_server_choice = $is_server_choice === 1 || (isset($command['is_server_choice']) && $command['is_server_choice'] == 'on') ? 1 : 0;
                if (isset($command['commands'])) {
                    foreach ($command['commands'] as $subCommand)
                    {
                        $commands[] = $subCommand;
                    }
                }
                if (isset($command['servers'])){
                    
                    foreach ($command['servers'] as $subServer)
                    {
                        $servers[] = intval($subServer);
                    }
                }
            }
        }
        $servers = array_unique($servers);
        foreach ($servers as $commandServer){
            if (empty($commandServer) || $commandServer == 'ALL'){
                $servers = [];
                break;
            }
        }
        /*for ($i = 0; $i < count($commands); $i++){
            $commands[$i]['servers'] = $servers;
        }*/

        $data['cmd'] = $request->has('cmd') && $request->input('cmd') == 'on' ? 1 : 0;
        $data['commands'] = $commands;

        $refCode = RefCode::find($id)->update($data);

        $commandsToDelete = Command::where('item_type', Command::REF_COMMAND)->where('item_id', $id)->select('id')->get()->pluck('id')->toArray();
        if (!empty($commandsToDelete)){
            $deletePlaceholder = implode(",", array_fill(0, count($commandsToDelete), '?'));
            DB::delete('DELETE item_servers FROM item_servers JOIN commands ON commands.id = item_servers.item_id WHERE item_servers.type = '.ItemServer::TYPE_REF_COMMAND_SERVER.' AND commands.item_id IN ('.$deletePlaceholder.')', $commandsToDelete);
        }

        Command::where('item_type', Command::REF_COMMAND)->where('item_id', $id)->delete();

        for ($i = 0; $i < count($commands); $i++){
            $delayValue = intval($commands[$i]['delay_value']);
            $delayUnit = intval($commands[$i]['delay_unit']);
            if ($delayValue > 0)
                $delayValue = CommandHelper::GetDelayValueSeconds($delayUnit, $delayValue);

            $repeatValue = intval($commands[$i]['repeat_value']);
            $repeatUnit = intval($commands[$i]['repeat_unit']);
            if ($repeatValue > 0)
                $repeatValue = CommandHelper::GetDelayValueSeconds($repeatUnit, $repeatValue);

            $command = Command::create([
                'item_type' => Command::REF_COMMAND,
                'item_id' => $id,
                'command' => $commands[$i]['command'],
                'event' => $commands[$i]['event'],
                'is_online_required' => $commands[$i]['is_online_required'] ?? 0,
                'delay_value' => $delayValue,
                'delay_unit' => $delayUnit,
                'repeat_unit' => $repeatUnit,
                'repeat_value' => $repeatValue,
                'repeat_cycles' => $commands[$i]['repeat_cycles'] ?? 0,
            ]);

            if (!empty($commands[$i]['servers'])){
                $isSpecificServers = true;
                foreach ($commands[$i]['servers'] as $commandServer) {
                    if (empty($commandServer) || $commandServer == 'ALL')
                        $isSpecificServers = false;
                }

                if ($isSpecificServers){
                    foreach ($commands[$i]['servers'] as $commandServer) {
                        if (empty($commandServer) || $commandServer == 'ALL')
                            continue;
                        
                        ItemServer::create([
                            'type' => ItemServer::TYPE_REF_COMMAND_SERVER,
                            'item_id' => $id,
                            'server_id' => $commandServer,
                            'cmd_id' => $command->id,
                        ]);
                    }
                }
            }
        }
        
        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['referers'],
        ]);

        return to_route('refs.show', $id);
    }

    /**
     * Delete from database
     * @param int $id
     * @return RedirectResponse
     * @return RedirectResponse|JsonResponse
     */

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        if (!UsersController::hasRule('referers', 'del')) {
            return redirect('/admin');
        }

        try {
            $refcode = RefCode::find($id);

            if (!$refcode){
                var_dump('Ref code not found');
            }

            $refcode->update([
                'referer' => $refcode->referer . '_deleted',
                'code' => $refcode->code . '_deleted',
                'deleted' => 1
            ]);

            Command::where('item_type', Command::REF_COMMAND)->where('item_id', $id)->delete();
            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['referers'],
                'action_id' => $id,
            ]);
        } catch (\Exception $e) {
        }

        if (request()->has('ajax'))
            return response()->json(['status' => 'true']);

        return to_route('refs.index');
    }
}
