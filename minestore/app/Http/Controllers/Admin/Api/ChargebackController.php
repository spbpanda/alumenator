<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Models\Chargeback;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\SecurityLog;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;

class ChargebackController extends Controller
{
    /**
     * API endpoint to get chargeback list
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('fraud', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }


        //$query = Chargeback::with(['user:username', 'payment:id,price,currency']);
        $query = Chargeback::query()->select([
            'chargebacks.id', 'chargebacks.status', 'chargebacks.creation_date',
            'users.username', 'payments.id as payment_id', 'payments.price', 'payments.currency'
        ])->join('payments', 'payments.id', '=', 'chargebacks.payment_id')
            ->join('users', 'users.id', '=', 'payments.user_id');

        // Column sorting
        if ($request->has('order')) {
            $orderBy = $request->order[0]['column'];
            $orderType = $request->order[0]['dir'];
            $column = $request->columns[$orderBy]['data'];

            $query->orderBy($column, $orderType);
        }

        // Searching by username and id
        if ($request->has('search') && $request->search['value'] != null) {
            $search = $request->search['value'];
            $type = intval($search) === 0 ? 'users.username' : 'chargebacks.id';
            $query->where($type, 'LiKE', "$search%");
        }

        $startIndex = (int)$request->input('start');
        $length = (int)$request->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $chargebacks = $query->paginate($length);

        return response()->json([
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => $chargebacks->total(),
            "recordsFiltered" => $chargebacks->total(),
            "data" => $chargebacks->items(),
        ]);
    }

    public function settings()
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return redirect('/admin');
        }

        return view('admin.chargeback.settings');
    }

    public function settingsSave(Request $r)
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->find(1)->update([
            'cb_threshold' => $r->input('cb_threshold'),
            'cb_period' => $r->input('cb_period'),
            'cb_username' => $r->input('cb_username') == 'on' ? 1 : 0,
            'cb_ip' => $r->input('cb_ip') == 'on' ? 1 : 0,
            'cb_bypass' => $r->input('cb_bypass'),
            'cb_local' => $r->input('cb_local'),
        ]);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::UPDATE_METHOD,
            'action' => SecurityLog::ACTION['fraud'],
        ]);

        return redirect('/admin/chargeback/settings');
    }

    /**
     * To mark a chargeback as COMPLETED
     * @param int $id
     * @return JsonResponse
     */
    public function finish(int $id): JsonResponse
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Chargeback::where('id', $id)->update([
            'status' => Chargeback::COMPLETED,
        ]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Remove chargeback from database
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (!UsersController::hasRule('fraud', 'delete')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        Chargeback::destroy($id);

        SecurityLog::create([
            'admin_id' => \Auth::guard('admins')->user()->id,
            'method' => SecurityLog::DELETE_METHOD,
            'action' => SecurityLog::ACTION['fraud'],
            'action_id' => $id,
        ]);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function submit(int $id): JsonResponse
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $chargeback = Chargeback::findOrFail($id);
        $payments = Payment::where('user_id', $chargeback->payment->user_id)->get();
        $settings = $this->settings;

        $paymentMethod = PaymentMethod::where('name', 'Stripe')->first();
        $config = json_decode($paymentMethod->config, true);
        $stripe = new \Stripe\StripeClient($config['private']);
        Pdf::loadView('pdfs.evidence', compact('chargeback', 'payments', 'settings'))->save('evidence.pdf');
        $fp = fopen('evidence.pdf', 'r');
        $stripe_file = $stripe->files->create([
            'purpose' => 'dispute_evidence',
            'file' => $fp,
        ]);

        try {
            $stripe->disputes->update(json_decode($chargeback->details, true)['case_id'],
                ['evidence' => [
                    'access_activity_log' => 'The user has purchased the digital item on our Minecraft webstore and received the product. Purchase was done by using this IP (' . $chargeback->payment->ip . '). We\'ve generated and attached the receipt that confirms buyer intentions to purchase this digital product.',
                    'product_description' => 'Digital product (rank/perk/item and etc) for Minecraft Server.',
                    'uncategorized_file' => $stripe_file,
                ],
                ]
            );
        } catch (ApiErrorException $e) {
            return response()->json(['message' => 'Stripe is temporary down, try later'], Response::HTTP_SERVICE_UNAVAILABLE);
        }


        return response()->json(['message' => 'Success']);
    }

    public function spendinglimit()
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return redirect('/admin');
        }
        return view('admin.chargeback.spendinglimit');
    }

    public function spendinglimitSave(Request $r)
    {
        if (!UsersController::hasRule('fraud', 'write')) {
            return redirect('/admin');
        }

        Setting::query()->find(1)->update([
            'cb_limit' => $r->input('cb_limit'),
            'cb_limit_period' => $r->input('cb_limit_period'),
        ]);

        return redirect('/admin/chargeback/spendinglimit');
    }
}
