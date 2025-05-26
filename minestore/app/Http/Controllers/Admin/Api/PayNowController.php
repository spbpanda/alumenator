<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Models\PnAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PayNowController extends Controller
{
    /**
     * API endpoint to get alerts
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('settings', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $query = PnAlert::query()->select([
            'alert_id',
            'status',
            'custom_title',
            'custom_message',
            'action_link',
            'action_required_at',
            'created_at',
        ]);

        // Column sorting
        if ($request->order != null) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];
            $query->orderBy($column, $orderType);
        }

        // Searching by alert_id, custom_title, or custom_message
        if ($request->search != null && $request->search['value'] != null) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('alert_id', 'LIKE', "$search%")
                    ->orWhere('custom_title', 'LIKE', "$search%")
                    ->orWhere('custom_message', 'LIKE', "$search%");
            });
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $alerts = $query->paginate($length);

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $alerts->total(),
            'recordsFiltered' => $alerts->total(),
            'data' => $alerts->items(),
        ]);
    }
}
