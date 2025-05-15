<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\BanUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Jobs\Utils;
use App\Models\Ban;

class BansController extends Controller
{
    /**
     * API endpoint to get banned users
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'read')) {
            return response()->json(['message' => 'Not authorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $query = Ban::query();

        // Column sorting
        if ($request->order != null) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];

            $query->orderBy($column, $orderType);
        }

        // Searching by username, ip address and uuid
        if ($request->search != null && $request->search['value'] != null) {
            $search = $request->search['value'];
            $type = SortHelper::getSearchType($search);
            $query->where($type, 'LiKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $banlist = $query->paginate($length);

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $banlist->total(),
            "recordsFiltered" => $banlist->total(),
            "data" => $banlist->items(),
        ]);
    }

    /**
     * API endpoint to ban user
     * @param BanUserRequest $request
     * @return JsonResponse
     */
    public function store(BanUserRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'write')) {
            return response()->json(['message' => 'Not authorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $banQuery = [['username', $request->username]];
        if ($request->has('ip')) {
            $banQuery[] = ['ip', $request->ip];
        }

        if($request->has('reason')) {
            $banQuery[] = ['reason', $request->reason];
        }

        // If user already banned
        if (Ban::where($banQuery)->exists()) {
            return response()->json(['message' => __('User has been banned already!')], Response::HTTP_GONE);
        }

        $uuid = null;
        $uuid_json = file_get_contents('https://minestorecms.com/api/uuid/name/' . $request->input('username'));
        if ($uuid_json) {
            $uuid_temp = json_decode($uuid_json, true);
            $uuid = $uuid_temp['uuid'];
        }

        $data = $request->validated();
        $data['uuid'] = $uuid;

        // Create ban record
        $ban = Ban::create($data);

        $ip = $request->ip ?? '';
        Utils::dispatch([
            'method' => 'p',
            'ban' => 1,
            'nick' => $request->input('username'),
            'ip' => $ip,
        ]);

        return response()->json($ban->toArray());
    }

    /**
     * API endpoint to unban user
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Ban::destroy($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
