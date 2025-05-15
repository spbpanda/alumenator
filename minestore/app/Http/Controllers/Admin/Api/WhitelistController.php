<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\StoreWhitelistRequest;
use App\Models\Whitelist;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * API Controller for Whitelist
 */
class WhitelistController extends Controller
{

    /**
     * API endpoint to get users in whitelist
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'read')) {
            return response()->json(['message' => 'Not authorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $query = Whitelist::query();

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

        $whitelist = $query->paginate($length);

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $whitelist->total(),
            "recordsFiltered" => $whitelist->total(),
            "data" => $whitelist->items(),
        ]);
    }

    /**
     * API endpoint to add user to the whitelist
     * @param StoreWhitelistRequest $request
     * @return JsonResponse
     */
    public function store(StoreWhitelistRequest $request): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $whitelistQuery = [['username', $request->username]];
        if ($request->has('ip')) {
            $whitelistQuery[] = ['ip', $request->ip];
        }

        if($request->has('reason')) {
            $whitelistQuery[] = ['reason', $request->reason];
        }

        // If user already whitelisted
        if (Whitelist::where($whitelistQuery)->exists()) {
            return response()->json(['message' => __('The user is in the whitelist already!')], Response::HTTP_GONE);
        }

        $whitelist = Whitelist::create($request->validated());

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::CREATE_METHOD,
            'action' => SecurityLog::ACTION['whitelist'],
            'action_id' => $whitelist->id,
        ]);

        return response()->json($whitelist->toArray());
    }

    /**
     * API endpoint to add user to whitelist
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (!UsersController::hasRule('bans', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Whitelist::destroy($id);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['whitelist'],
            'action_id' => $id,
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
