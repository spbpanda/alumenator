<?php

namespace App\Http\Controllers\Admin;

use App\Facades\PaynowManagement;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Subscriptions'));
    }

    public function index()
    {
        if (! UsersController::hasRule('subs', 'read')) {
            return redirect('/admin');
        }

        $subscriptions = Subscription::orderBy('id', 'desc')->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show($id)
    {
        if (!UsersController::hasRule('subs', 'read')) {
            return redirect('/admin');
        }

        $subscription = Subscription::findOrFail($id);
        if (!$subscription) {
            return redirect('/admin/subscriptions')->with('error', __('Subscription not found'));
        }

        $payment = $subscription->payment;
        $user = $subscription->user;

        return view('admin.subscriptions.show', compact('subscription', 'payment', 'user'));
    }

    public function closeSubscription($id): JsonResponse
    {
        if (!UsersController::hasRule('subs', 'write')) {
            return response()->json([
                'status' => false,
                'message' => 'Permission denied'
            ], 403);
        }

        $subscription = Subscription::find($id);
        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription not found'
            ], 404);
        }

        try {
            $payment = $subscription->payment;
            if ($payment) {
                $gateway = strtolower($payment->gateway);

                switch ($gateway) {
                    case 'stripe':
                        $paymentMethod = PaymentMethod::where('name', 'Stripe')->first();
                        if ($paymentMethod && $paymentMethod->enable) {
                            $config = json_decode($paymentMethod->config, true);
                            \Stripe\Stripe::setApiKey($config['private']);
                            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->sid);
                            if ($stripeSubscription) {
                                $stripeSubscription->cancel();
                            }
                        }
                        break;
                    case 'paynow':
                        $payNowSubscription = PaynowManagement::getSubscription($subscription->sid);
                        if (!$payNowSubscription) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Failed to retrieve PayNow subscription'
                            ], 400);
                        }

                        $cancelResult = PaynowManagement::cancelSubscription($subscription->sid);
                        if (!$cancelResult) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Failed to cancel subscription'
                            ], 400);
                        }
                        break;
                    default:
                        return response()->json([
                            'status' => false,
                            'message' => 'Unsupported payment gateway'
                        ], 400);
                }
            }

            $subscription->status = Subscription::CANCELLED;
            $subscription->save();

            return response()->json([
                'status' => true,
                'message' => 'Subscription closed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error closing subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    public function datatables(Request $r)
    {
        if (!UsersController::hasRule('subs', 'read')) {
            return redirect('/admin');
        }

        $startIndex = (int)$r->input('start');
        $length = (int)$r->input('length');
        $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $subs = Subscription::query()
        ->with(['user' => function ($query) {
            $query->select('username');
        }])
        ->with(['payment' => function ($query) {
            $query->select('id', 'gateway');
        }])->orderBy('id', 'DESC')->paginate($length);

        return [
            "draw" => (int)$r->input('draw'),
            "recordsTotal" => $subs->total(),
            "recordsFiltered" => $subs->total(),
            "data" => $subs->items(),
        ];
    }
}
