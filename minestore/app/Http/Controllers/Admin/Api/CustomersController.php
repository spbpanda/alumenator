<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Controller;
use App\Models\Chargeback;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    /**
     * API endpoint to get customers list
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $cacheKey = 'customers_list ' . $request->input('draw', 1);

        return Cache::remember($cacheKey, 120, function () use ($request) {
            $allowedColumns = [
                'users.id', 'users.username', 'users.created_at',
                'users.country_code', 'users.uuid', 'users.country'
            ];

            $columnMap = [
                'id' => 'users.id',
                'username' => 'users.username',
                'created_at' => 'users.created_at',
                'country_code' => 'users.country_code',
                'country' => 'users.country',
                'uuid' => 'users.uuid',
                'human_date' => 'users.created_at'
            ];

            $query = User::query()
                ->whereNotIn('username', ['root', 'xMarkus', 'test'])
                ->select($allowedColumns);

            if ($request->has('order')) {
                $orderBy = $request->order[0]['column'];
                $orderType = $request->order[0]['dir'];
                $columnName = $request->columns[$orderBy]['data'];

                $actualColumn = $columnMap[$columnName] ?? null;

                if ($actualColumn && in_array($actualColumn, $allowedColumns)) {
                    $query->orderBy($actualColumn, $orderType);
                } else {
                    $query->orderBy('users.id', 'desc');
                }
            }

            // Searching by username and uuid
            if ($request->has('search') && $request->search['value'] != null) {
                $search = $request->search['value'];
                $type = (int)$search === 0 ? 'users.username' : 'users.uuid';
                $query->where($type, 'LIKE', "$search%");
            }

            $startIndex = (int)$request->input('start');
            $length = (int)$request->input('length');
            $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $chargebacks = $query->paginate($length);

            $data = collect($chargebacks->items())->map(function ($item) {
                $item->human_date = $item->created_at->diffForHumans();
                return $item;
            });

            return response()->json([
                "draw" => (int)$request->input('draw'),
                "recordsTotal" => $chargebacks->total(),
                "recordsFiltered" => $chargebacks->total(),
                "data" => $data,
            ]);
        });
    }

    public function getPurchasedPackages(Request $request, $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cacheKey = 'customer_purchased_packages_' . $id . '_' . $request->input('draw', 1);

        return Cache::remember($cacheKey, 300, function () use ($request, $user) {
            $url = '/img/items/';

            $allowedColumns = [
                'items.id',
                'items.name',
                'items.image',
                'payments.status',
                'cart_items.payment_type',
                'subscriptions.renewal',
                'payments.created_at'
            ];

            $columnMap = [
                'id' => 'items.id',
                'package_name' => 'items.name',
                'package_image' => 'items.image',
                'status' => 'payments.status',
                'type' => 'cart_items.payment_type',
                'expiration_at' => 'subscriptions.renewal',
                'purchased_at' => 'payments.created_at',
                'human_date' => 'payments.created_at'
            ];

            $query = Payment::where('payments.user_id', $user->id)
                ->whereIn('payments.status', [Payment::COMPLETED, Payment::PAID])
                ->join('carts', 'payments.cart_id', '=', 'carts.id')
                ->join('cart_items', 'carts.id', '=', 'cart_items.cart_id')
                ->join('items', 'cart_items.item_id', '=', 'items.id')
                ->leftJoin('subscriptions', 'payments.id', '=', 'subscriptions.payment_id')
                ->select($allowedColumns);

            if ($request->has('order')) {
                $orderBy = $request->order[0]['column'];
                $orderType = $request->order[0]['dir'];
                $columnName = $request->columns[$orderBy]['data'];

                $actualColumn = $columnMap[$columnName] ?? null;

                if ($actualColumn && in_array($actualColumn, $allowedColumns)) {
                    $query->orderBy($actualColumn, $orderType);
                } else {
                    $query->orderBy('payments.created_at', 'desc');
                }
            } else {
                $query->orderBy('payments.created_at', 'desc');
            }

            // Handle searching
            if ($request->has('search') && $request->search['value'] != null) {
                $search = $request->search['value'];
                $query->where('items.name', 'LIKE', "%$search%");
            }

            $startIndex = (int)$request->input('start', 0);
            $length = (int)$request->input('length', 10);
            $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $packages = $query->paginate($length);

            $data = collect($packages->items())->map(function ($item) use ($url) {
                return [
                    'id' => $item->id,
                    'package_name' => $item->name,
                    'package_image' => $url . $item->image,
                    'status' => $item->status,
                    'type' => $item->payment_type,
                    'expiration_at' => $item->renewal ? date('Y-m-d H:i:s', strtotime($item->renewal)) : null,
                    'purchased_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'human_date' => Carbon::parse($item->created_at)->diffForHumans()
                ];
            });

            return response()->json([
                "draw" => (int)$request->input('draw', 1),
                "recordsTotal" => $packages->total(),
                "recordsFiltered" => $packages->total(),
                "data" => $data,
            ]);
        });
    }

    public function getTransactions(Request $request, $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cacheKey = 'customer_transactions_' . $id . '_' . $request->input('draw', 1);

        return Cache::remember($cacheKey, 300, function () use ($request, $user) {
            $url = '/img/items/';

            $allowedColumns = [
                'payments.id',
                'payments.status',
                'payments.created_at',
                'carts.price',
                'carts.id as cart_id',
            ];

            $columnMap = [
                'id' => 'payments.id',
                'status' => 'payments.status',
                'created_at' => 'payments.created_at',
                'price' => 'carts.price',
            ];

            $query = Payment::where('payments.user_id', $user->id)
                ->join('carts', 'payments.cart_id', '=', 'carts.id')
                ->leftJoin('cart_items', 'carts.id', '=', 'cart_items.cart_id')
                ->leftJoin('items', 'cart_items.item_id', '=', 'items.id')
                ->select($allowedColumns)
                ->groupBy('payments.id', 'carts.id', 'carts.price', 'payments.status', 'payments.created_at');

            if ($request->has('order')) {
                $orderBy = $request->order[0]['column'];
                $orderType = $request->order[0]['dir'];
                $columnName = $request->columns[$orderBy]['data'];

                $actualColumn = $columnMap[$columnName] ?? null;

                if ($actualColumn && in_array($actualColumn, $allowedColumns)) {
                    $query->orderBy($actualColumn, $orderType);
                } else {
                    $query->orderBy('payments.created_at', 'desc');
                }
            } else {
                $query->orderBy('payments.created_at', 'desc');
            }

            if ($request->has('search') && $request->search['value'] != null) {
                $search = $request->search['value'];
                $query->where('items.name', 'LIKE', "%$search%");
            }

            $startIndex = (int)$request->input('start', 0);
            $length = (int)$request->input('length', 10);
            $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $transactions = $query->paginate($length);

            $data = collect($transactions->items())->map(function ($payment) use ($url) {
                $items = DB::table('cart_items')
                    ->leftJoin('items', 'cart_items.item_id', '=', 'items.id')
                    ->where('cart_items.cart_id', $payment->cart_id)
                    ->select(
                        'items.id as item_id',
                        'items.name as package_name',
                        'items.image as package_image'
                    )
                    ->get()
                    ->map(function ($item) use ($url) {
                        return [
                            'item_id' => $item->item_id,
                            'package_name' => $item->package_name ?? 'Unknown Item',
                            'package_image' => $item->package_image ? $url . $item->package_image : null,
                        ];
                    });

                return [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'price' => $payment->price ?? 0,
                    'items' => $items,
                    'human_date' => Carbon::parse($payment->created_at)->diffForHumans(),
                ];
            });

            return response()->json([
                "draw" => (int)$request->input('draw', 1),
                "recordsTotal" => $transactions->total(),
                "recordsFiltered" => $transactions->total(),
                "data" => $data,
            ]);
        });
    }

    public function getSubscriptions(Request $request, $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cacheKey = 'customer_subscriptions_' . $id . '_' . $request->input('draw', 1);

        return Cache::remember($cacheKey, 300, function () use ($request, $user) {
            $url = '/img/items/';

            $allowedColumns = [
                'subscriptions.id',
                'subscriptions.renewal',
                'subscriptions.creation_date',
                'subscriptions.status',
                'subscriptions.count',
                'carts.price',
                'cart_items.item_id',
                'items.name as package_name',
                'items.image as package_image',
            ];

            $columnMap = [
                'id' => 'subscriptions.id',
                'package_name' => 'items.name',
                'status' => 'subscriptions.status',
                'billing_cycles' => 'subscriptions.count',
                'renewal_date' => 'subscriptions.renewal',
                'created_at' => 'subscriptions.creation_date',
            ];

            $query = Subscription::join('payments', 'subscriptions.payment_id', '=', 'payments.id')
                ->where('payments.user_id', $user->id)
                ->join('carts', 'payments.cart_id', '=', 'carts.id')
                ->leftJoin('cart_items', 'carts.id', '=', 'cart_items.cart_id')
                ->leftJoin('items', 'cart_items.item_id', '=', 'items.id')
                ->select($allowedColumns)
                ->groupBy('subscriptions.id', 'carts.id', 'subscriptions.renewal', 'subscriptions.creation_date',
                    'subscriptions.status', 'subscriptions.count', 'carts.price',
                    'cart_items.item_id', 'items.name', 'items.image');

            if ($request->has('order')) {
                $orderBy = $request->order[0]['column'];
                $orderType = $request->order[0]['dir'];
                $columnName = $request->columns[$orderBy]['data'];

                $actualColumn = $columnMap[$columnName] ?? null;

                if ($actualColumn && in_array($actualColumn, $allowedColumns)) {
                    $query->orderBy($actualColumn, $orderType);
                } else {
                    $query->orderBy('subscriptions.creation_date', 'desc');
                }
            } else {
                $query->orderBy('subscriptions.creation_date', 'desc');
            }

            if ($request->has('search') && $request->search['value'] != null) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('items.name', 'LIKE', "%$search%")
                        ->orWhere('subscriptions.id', 'LIKE', "%$search%")
                        ->orWhere('subscriptions.status', 'LIKE', "%$search%");
                });
            }

            $startIndex = (int)$request->input('start', 0);
            $length = (int)$request->input('length', 10);
            $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $subscriptions = $query->paginate($length);

            $data = collect($subscriptions->items())->map(function ($subscription) use ($url) {
                $item = DB::table('cart_items')
                    ->leftJoin('items', 'cart_items.item_id', '=', 'items.id')
                    ->leftJoin('carts', 'cart_items.cart_id', '=', 'carts.id')
                    ->leftJoin('payments', 'carts.id', '=', 'payments.cart_id')
                    ->leftJoin('subscriptions', 'payments.id', '=', 'subscriptions.payment_id')
                    ->where('subscriptions.id', $subscription->id)
                    ->select(
                        'items.id as item_id',
                        'items.name as package_name',
                        'items.image as package_image'
                    )
                    ->first();

                $renewalDate = $subscription->renewal ? Carbon::parse($subscription->renewal)->format('Y-m-d') : null;
                $createdDate = $subscription->creation_date ? Carbon::parse($subscription->creation_date)->format('Y-m-d H:i:s') : null;

                $renewalHumanDate = $subscription->renewal ? Carbon::parse($subscription->renewal)->diffForHumans() : null;

                return [
                    'id' => $subscription->id,
                    'package_name' => $item ? $item->package_name : 'Unknown Package',
                    'package_image' => $item && $item->package_image ? $url . $item->package_image : null,
                    'item_id' => $item ? $item->item_id : null,
                    'status' => $subscription->status,
                    'billing_cycles' => $subscription->count ?? 0,
                    'renewal_date' => $renewalHumanDate,
                    'renewal_date_raw' => $renewalDate,
                    'created_at' => $createdDate,
                    'human_date' => $subscription->creation_date ? Carbon::parse($subscription->creation_date)->diffForHumans() : null,
                    'price' => $subscription->price ?? 0,
                ];
            });

            return response()->json([
                "draw" => (int)$request->input('draw', 1),
                "recordsTotal" => $subscriptions->total(),
                "recordsFiltered" => $subscriptions->total(),
                "data" => $data,
            ]);
        });
    }

    public function getChargebacks(Request $request, $id): JsonResponse
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cacheKey = 'customer_chargebacks_' . $id . '_' . $request->input('draw', 1);

        return Cache::remember($cacheKey, 300, function () use ($request, $user) {

            $allowedColumns = [
                'chargebacks.id',
                'chargebacks.creation_date',
                'chargebacks.status',
                'chargebacks.sid',
                'chargebacks.payment_id',
                'carts.price'
            ];

            $columnMap = [
                'id' => 'chargebacks.id',
                'created_at' => 'chargebacks.creation_date',
                'status' => 'chargebacks.status',
                'sid' => 'chargebacks.sid',
                'payment_id' => 'chargebacks.payment_id',
                'price' => 'carts.price'
            ];

            $query = Chargeback::join('payments', 'chargebacks.payment_id', '=', 'payments.id')
                ->where('payments.user_id', $user->id)
                ->join('carts', 'payments.cart_id', '=', 'carts.id')
                ->select($allowedColumns);

            if ($request->has('order')) {
                $orderBy = $request->order[0]['column'];
                $orderType = $request->order[0]['dir'];
                $columnName = $request->columns[$orderBy]['data'];

                $actualColumn = $columnMap[$columnName] ?? null;

                if ($actualColumn && in_array($actualColumn, $allowedColumns)) {
                    $query->orderBy($actualColumn, $orderType);
                } else {
                    $query->orderBy('chargebacks.creation_date', 'desc');
                }
            } else {
                $query->orderBy('chargebacks.creation_date', 'desc');
            }

            if ($request->has('search') && $request->search['value'] != null) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('chargebacks.id', 'LIKE', "%$search%")
                        ->orWhere('chargebacks.sid', 'LIKE', "%$search%")
                        ->orWhere('chargebacks.status', 'LIKE', "%$search%");
                });
            }

            $startIndex = (int)$request->input('start', 0);
            $length = (int)$request->input('length', 10);
            $currentPage = 1 + ($startIndex == 0 ? 0 : ($startIndex / $length));
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $chargebacks = $query->paginate($length);

            $data = collect($chargebacks->items())->map(function ($chargeback) {
                $createdDate = $chargeback->creation_date ? Carbon::parse($chargeback->creation_date)->format('Y-m-d H:i:s') : null;

                return [
                    'id' => $chargeback->id,
                    'sid' => $chargeback->sid,
                    'status' => $chargeback->status,
                    'price' => $chargeback->price ?? 0,
                    'created_at' => $createdDate,
                    'human_date' => $chargeback->creation_date ? Carbon::parse($chargeback->creation_date)->diffForHumans() : null,
                    'payment_id' => $chargeback->payment_id
                ];
            });

            return response()->json([
                "draw" => (int)$request->input('draw', 1),
                "recordsTotal" => $chargebacks->total(),
                "recordsFiltered" => $chargebacks->total(),
                "data" => $data,
            ]);
        });
    }
}
