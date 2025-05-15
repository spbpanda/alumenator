<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\BanUserRequest;
use App\Models\Ban;
use App\Models\RefCode;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RefsController extends Controller
{
    /**
     * API endpoint to get banned users
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('referers', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        /*$refs = DB::select('select (SELECT IFNULL(SUM(`carts`.`price`) * (`ref_codes`.`percent` / 100),0) FROM `payments` join `carts` on `carts`.`id` = `payments`.`cart_id` WHERE `payments`.`ref` =  `ref_codes`.`id` AND `payments`.`status` = 1 OR `payments`.`status` = 3) as amount,
            (SELECT COUNT(DISTINCT `payments`.`user_id`) FROM `payments` WHERE `payments`.`ref` = `ref_codes`.`id`) as refs,
            `ref_codes`.`id`,
            `ref_codes`.`referer`,
            `ref_codes`.`percent`,
            `ref_codes`.`code`
            from `ref_codes`
            order by `amount` desc');*/

        $query = RefCode::query()->where('deleted', 0)->select(
            DB::raw('(SELECT IFNULL(ROUND(SUM(carts.price) * (ref_codes.percent / 100),2), 0) FROM payments
                 JOIN carts ON carts.id = payments.cart_id
                 WHERE payments.ref = ref_codes.id AND (payments.status = 1 OR payments.status = 3)) as amount'),
            DB::raw('(SELECT COUNT(DISTINCT payments.user_id) FROM payments
                 WHERE payments.ref = ref_codes.id) as refs'),
            'ref_codes.id',
            'ref_codes.referer',
            'ref_codes.percent',
            'ref_codes.code'
        );

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
            $query->where('ref_codes.referer', 'LiKE', "$search%")
                ->orWhere('ref_codes.code', 'LIKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $bans = $query->paginate($length);

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $bans->total(),
            'recordsFiltered' => $bans->total(),
            'data' => $bans->items(),
        ]);
    }

    /**
     * API endpoint to remove ref code
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if (!UsersController::hasRule('referers', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $refcode = RefCode::find($id);

        if (!$refcode) {
            return response()->json(['message' => __('Referral code does not exist.')], Response::HTTP_NOT_FOUND);
        }

        $refcode->update([
            'referer' => $refcode->referer . '_deleted',
            'code' => $refcode->code . '_deleted',
            'deleted' => 1
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * API endpoint to enable/disable ref module
     * @param Request $r
     * @return JsonResponse
     */
    public function enabledSave(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('referers', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Setting::query()->find(1)->update([
            'is_ref' => $r->input('isRefEnabled'),
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
