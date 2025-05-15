<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Models\Admin;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecurityLogsController extends Controller
{
    /**
     * API endpoint to get users in whitelist
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('teams', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $query = SecurityLog::select([
            'security_logs.id',
            'security_logs.method',
            'security_logs.action',
            'security_logs.action_id',
            'security_logs.extra',
            'security_logs.created_at',
            'admins.username',
        ])
            ->crossJoin('admins', 'admins.id', '=', 'security_logs.admin_id');

        // Column sorting
        if ($request->order != null) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];

            $query->orderBy('security_logs.' . $column, $orderType);
        }

        // Searching by username
        if ($request->search != null && $request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where('admins.username', 'LIKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $logs = $query->paginate($length);

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $logs->total(),
            "recordsFiltered" => $logs->total(),
            "data" => $logs->items(),
        ]);
    }
}
