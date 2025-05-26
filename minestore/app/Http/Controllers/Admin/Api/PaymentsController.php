<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\AddPaymentNoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Payment;
use App\Models\SecurityLog;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * API endpoint to get payments
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $query = Payment::query()->join('users', 'users.id', '=', 'payments.user_id')
            ->select(['payments.id', 'payments.price', 'payments.status', 'payments.updated_at',
                'payments.currency', 'users.username']);

        // Column sorting
        if ($request->order != null) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];
            $query->orderBy($column, $orderType);
        }

        // Searching by username and id
        if ($request->search != null && $request->search['value'] != null) {
            $search = $request->search['value'];
            $type = intval($search) === 0 ? 'users.username' : 'payments.id';
            $query->where($type, 'LiKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $payments = $query->paginate($length);

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $payments->total(),
            'recordsFiltered' => $payments->total(),
            'data' => $payments->items(),
        ]);
    }

    /**
     * API endpoint to deliver items
     * @param string $id
     * @return JsonResponse
     */
    public function delivery(string $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $payment = Payment::query()->with(['user', 'cart'])->find($id);

            ItemsController::giveItems($payment);

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::CREATE_METHOD,
                'action' => SecurityLog::ACTION['p_force_delivery'],
                'action_id' => $id,
            ]);

            return response()->json(['message' => __('All items were successfully delivered!')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('Unable to deliver the items!')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API endpoint to add payment note
     * @param AddPaymentNoteRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function note(AddPaymentNoteRequest $request, string $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Payment::where('id', $id)->update(['note' => $request->note]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['p_add_note'],
            'action_id' => $id,
        ]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * API endpoint to delete payment
     * @param string $id
     * @return JsonResponse
     * @throws \Throwable The exception that is thrown when a database transaction fails.
     */
    public function destroy(string $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'del')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            DB::beginTransaction();

            DB::table('discord_role_queue')->where('payment_id', $id)->delete();

            Payment::destroy($id);

            SecurityLog::create([
                'admin_id' => \Auth::guard('admins')->user()->id,
                'method' => SecurityLog::DELETE_METHOD,
                'action' => SecurityLog::ACTION['payments'],
                'action_id' => $id,
            ]);

            DB::commit();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error deleting payment: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API endpoint to enable/disable collecting payment data during checkout
     * @param Request $r
     * @return JsonResponse
     */
    public function enabledSave(Request $r): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Setting::query()->find(1)->update([
            'details' => $r->input('isDetailsEnabled'),
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
