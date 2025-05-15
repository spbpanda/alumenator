<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Admin;
use App\Models\SecurityLog;

class AdminController extends Controller
{
    /**
     * API endpoint to get users
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('teams', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $query = Admin::query();

        // Column sorting
        if ($request->order != null) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];

            $query->orderBy($column, $orderType);
        }

        // Searching by username
        if ($request->search != null && $request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where('username', 'LiKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $users = $query->paginate($length);

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $users->total(),
            "recordsFiltered" => $users->total(),
            "data" => $users->items(),
        ]);
    }

    /**
     * API endpoint to remove user
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if (!UsersController::hasRule('teams', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Admin::destroy($id);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['teams'],
            'action_id' => $id,
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
