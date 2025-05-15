<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\StoreWhitelistRequest;
use App\Models\Setting;
use App\Models\Gift;
use App\Models\SecurityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * API Controller for Giftcards
 */
class GiftcardsController extends Controller
{

    /**
     * API endpoint to get giftcards
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('discounts', 'read')) {
            return response()->json(['message' => 'Not authorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $query = Gift::query()->where('deleted', 0);

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

        $giftcards = $query->paginate($length);
        $currency = Setting::get('currency')->first();

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $giftcards->total(),
            "recordsFiltered" => $giftcards->total(),
            "data" => $giftcards->items(),
            "currency" => $currency,
        ]);
    }

    /**
     * API endpoint to remove Giftcards
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (!UsersController::hasRule('discounts', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $gift = Gift::where('id', $id)->first();
        $gift->name = 'DELETED-' .  $gift->name;
        $gift->deleted = 1;
        $gift->save();

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['coupons'],
            'action_id' => $id,
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
