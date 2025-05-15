<?php

namespace App\Http\Controllers\Admin\Api\Settings;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Models\SecurityLog;
use App\Models\Server;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ServerSettingsController extends Controller
{
    /**
     * Store a server
     * @param StoreServerRequest $request
     * @return JsonResponse
     */
    public function store(StoreServerRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $server = Server::updateOrCreate(['name' => $request->name], $request->validated());

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['servers'],
            'action_id' => $server->id,
        ]);

        return response()->json(['id' => $server->id]);
    }

    /**
     * Update a server
     * @param UpdateServerRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateServerRequest $request, string $id): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Server::where('id', $id)->update($request->validated());

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['servers'],
            'action_id' => $id,
        ]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Delete a server
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $server = Server::findOrFail($id);
        $randomKey = 'deleted-' . Str::random(15);

        Server::where('id', $id)->update([
            'name' => '[DELETED] ' . $server->name,
            'secret_key' => $randomKey,
            'deleted' => 1
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['servers'],
            'action_id' => $id,
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Check server connection
     * @param string $id
     * @return JsonResponse
     */
    public function check(string $id): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $server = Server::findOrFail($id);

        if ($server->method == 'rcon') {
            if (\App\Http\Controllers\ItemsController::sendRcon('say MineStoreCMS.com RCON Delivery Method Connected Successfully', $server))
            {
                return response()->json(['message' => 'Success']);
            }
        } elseif ($server->method == 'websocket') {
            if (\App\Http\Controllers\ItemsController::sendWebsocket('say Official MineStoreCMS.com Plugin Connected Successfully', $server, 'admin', false))
            {
                return response()->json(['message' => 'Success']);
            }
        } elseif ($server->method == 'listener') {
            if (\App\Http\Controllers\ItemsController::sendListener('say Official MineStoreCMS.com Plugin Connected Successfully', 'admin_' . $id, false, null))
            {
                return response()->json(['message' => 'Success']);
            }
        }

        return response()->json(['message' => __('Failed to Connect to the Minecraft Server')], Response::HTTP_BAD_GATEWAY);
    }
}
