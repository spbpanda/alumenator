<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Cache;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Customers'));
    }

    public function index()
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return redirect('/admin');
        }

        $cacheTime = 10;

        $customers = Cache::remember('admin.customers.stats', $cacheTime, function () {
            return [
                'total' => User::count(),
                'total_this_month' => User::whereMonth('created_at', date('m'))->count(),
            ];
        });

        $country = Cache::remember('admin.country.stats', $cacheTime, function () {
            $countryQuery = DB::table('users')
                ->select(
                    DB::raw('CASE
                    WHEN country IS NULL OR country = "" THEN "Unknown"
                    ELSE TRIM(country)
                    END as normalized_country'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('normalized_country')
                ->orderByDesc('count');

            $countryStats = $countryQuery->get();

            $totalCountries = $countryStats->filter(function($item) {
                return $item->normalized_country != 'Unknown';
            })->count();

            $topCountry = $countryStats->first();
            $topCountryName = $topCountry ? ($topCountry->normalized_country == 'Unknown'
                ? ($countryStats->count() > 1 ? $countryStats[1]->normalized_country : 'N/A')
                : $topCountry->normalized_country)
                : 'N/A';

            return [
                'total' => $totalCountries,
                'top_country' => $topCountryName,
            ];
        });

        $averageSpent = Cache::remember('admin.payments.stats', $cacheTime, function () {
            $completedPaymentsCount = Payment::whereIn('status', [Payment::COMPLETED, Payment::PAID])->count();
            $totalSpent = Payment::whereIn('status', [Payment::COMPLETED, Payment::PAID])
                ->join('carts', 'payments.cart_id', '=', 'carts.id')
                ->sum('carts.price');

            return [
                'currency' => Setting::first()->currency,
                'total' => $completedPaymentsCount > 0 ? round(($totalSpent / $completedPaymentsCount), 2) : 0,
                'total_payments' => $completedPaymentsCount,
            ];
        });

        return view('admin.customers.index', compact('customers', 'country', 'averageSpent'));
    }

    public function show($id)
    {
        if (!UsersController::hasRule('payments', 'read')) {
            return redirect('/admin');
        }

        $customer = User::find($id);
        if (!$customer) {
            return redirect()->back()->with('error', __('Customer not found.'));
        }

        $currency = Setting::first()->currency;

        $customer->total_spent = Payment::join('carts', 'payments.cart_id', '=', 'carts.id')
            ->where('payments.user_id', $customer->id)
            ->whereIn('payments.status', [Payment::COMPLETED, Payment::PAID])
            ->sum('carts.price') ?? 0;

        $customer->total_orders = Payment::where('user_id', $customer->id)
            ->whereIn('payments.status', [Payment::COMPLETED, Payment::PAID])
            ->count() ?? 0;

        $customer->avg_spent = $customer->total_orders > 0
            ? round(($customer->total_spent / $customer->total_orders), 2)
            : 0;

        $customer->active_subscriptions = Payment::join('subscriptions', 'payments.id', '=', 'subscriptions.payment_id')
            ->where('subscriptions.status', Subscription::ACTIVE)
            ->where('payments.user_id', $customer->id)
            ->count() ?? 0;

        $customer->first_seen_at = $customer->created_at->format('F jS, Y');

        $query = Ban::where('username', $customer->username);

        if (!is_null($customer->uuid)) {
            $query->orWhere('uuid', $customer->uuid);
        }

        $customer->banned = $query->exists();

        $ban = null;

        if ($customer->banned) {
            $banQuery = Ban::where('username', $customer->username);

            if (!is_null($customer->uuid)) {
                $banQuery->orWhere('uuid', $customer->uuid);
            }

            $ban = $banQuery->first();
        }

        return view('admin.customers.show', compact('customer', 'currency', 'ban'));
    }
}
