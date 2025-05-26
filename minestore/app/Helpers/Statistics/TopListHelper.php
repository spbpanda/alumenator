<?php

namespace App\Helpers\Statistics;

use App\Helpers\CurrencyHelper;
use App\Helpers\ValuesDifferenceHelper;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TopListHelper
{
    public function getTopPackages($dates = null): object
    {
        $allPackages = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('items', 'cart_items.item_id', '=', 'items.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->join('categories', 'items.category_id', '=', 'categories.id');

        if (!is_null($dates)) {
            $allPackages = $allPackages->whereBetween('payments.created_at', [$dates['from'], $dates['until']]);
        } else {
            $allPackages = $allPackages->where('payments.created_at', '<=', now());
        }

        $allPackages = $allPackages->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->where('carts.virtual_price', 0)
            ->select(
                'items.name as package',
                'items.image as image',
                'categories.name as category_name',
                'items.id as id',
                'cart_items.price as item_price',
                'cart_items.count as item_count'
            )
            ->get();

        $allPackagesGrouped = $allPackages->groupBy('id')->map(function ($group) {
            $totalValue = $group->sum(function ($item) {
                return $item->item_price * $item->item_count;
            });

            return [
                'package' => $group->first()->package,
                'image' => $group->first()->image,
                'category_name' => $group->first()->category_name,
                'id' => $group->first()->id,
                'total_value' => $totalValue,
                'total_records' => $group->count()
            ];
        })->values();

        $totalSales = $allPackagesGrouped->sum('total_value');
        $topPackages = $allPackagesGrouped->sortByDesc('total_value')->take(5);
        $revenue = CurrencyHelper::formatMoney($topPackages->sum('total_value'));

        $categories = $topPackages->pluck('package')->toArray();
        $data = $topPackages->pluck('total_value')->toArray();
        foreach ($data as $key => $value) {
            if (!is_numeric($value)) {
                $value = CurrencyHelper::parseFormattedMoney($value);
            }

            if (!is_numeric($revenue)) {
                $revenue = CurrencyHelper::parseFormattedMoney($revenue);
            }

            if ($revenue != 0) {
                $data[$key] = round(($value / $revenue) * 100, 2);
            } else {
                $data[$key] = 0;
            }
        }

        $topPackages->each(function ($package) {
            $package['total_value'] = CurrencyHelper::formatMoney($package['total_value']);
            if (!is_numeric($package['total_value'])) {
                $package['total_value'] = CurrencyHelper::parseFormattedMoney($package['total_value']);
            }
        });

        return (object)[
            'totalSales' => $totalSales,
            'revenue' => $revenue,
            'packages' => $topPackages,
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getSalesByCountries($dates = null): Collection
    {
        $countriesTotal = DB::table('carts')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->where('payments.created_at', '<=', now())
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereNotNull('users.country')
            ->select(DB::raw('users.username'),
                DB::raw('users.country as country'),
                DB::raw('users.country_code as code'),
                DB::raw('(SUM(carts.items)) as total_records'),
                DB::raw('(SUM(carts.price)) as total_value'))
            ->orderBy('total_value', 'DESC')
            ->orderBy('total_records', 'DESC')
            ->groupBy(DB::raw('users.country'))
            ->limit(7)
            ->get();

        $countriesMonth = DB::table('carts')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereBetween('payments.created_at', [now()->startOfMonth(), now()])
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereNotNull('users.country')
            ->select(DB::raw(DB::raw('(SUM(carts.price)) as total_value')))
            ->orderBy('total_value', 'DESC')
            ->groupBy(DB::raw('users.country'))
            ->limit(5)
            ->get();

        $countriesPrevMonth = DB::table('carts')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereBetween('payments.created_at', [
                now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()
            ])
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereNotNull('users.country')
            ->select(DB::raw(DB::raw('(SUM(carts.price)) as total_value')))
            ->orderBy('total_value', 'DESC')
            ->groupBy(DB::raw('users.country'))
            ->limit(5)
            ->get();

        $countriesTotal->each(function ($country, $key) use ($countriesPrevMonth, $countriesMonth) {
            $thisMonthAmount = $countriesMonth->get($key)->total_value ?? 0;
            $prevMonthAmount = $countriesPrevMonth->get($key)->total_value ?? 0;

            $country->month_value = CurrencyHelper::formatMoney($thisMonthAmount);
            $country->total_value = CurrencyHelper::formatMoney($country->total_value);

            $country->difference = ValuesDifferenceHelper::getPercentageDifference($thisMonthAmount, $prevMonthAmount);
            $country->level = ($country->difference == 0) ? 'equal' : ($country->difference > 0 ? 'up' : 'down');
        });

        return $countriesTotal;
    }


    public function getSalesByCountriesBetween($dates = null): Collection
    {
        $countriesTotal = DB::table('carts')
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id');

        if (!is_null($dates)) {
            $countriesTotal = $countriesTotal->whereBetween('payments.created_at', [$dates['from'], $dates['until']]);
        } else {
            $countriesTotal = $countriesTotal->where('payments.created_at', '<=', now());
        }

        $countriesTotal = $countriesTotal->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->select(DB::raw('users.username'),
                DB::raw('users.country as country'),
                DB::raw('users.country_code as code'),
                DB::raw('(SUM(carts.items)) as total_records'),
                DB::raw('(SUM(carts.price)) as total_value'))
            ->orderBy('total_value', 'DESC')
            ->orderBy('total_records', 'DESC')
            ->groupBy(DB::raw('users.country'))
            ->limit(7)
            ->get();

        $countriesTotal->each(function ($country, $key) {
            $country->month_value = false;
            $country->total_value = CurrencyHelper::formatMoney($country->total_value);

            $country->difference = false;
            $country->level = false;
        });

        return $countriesTotal;
    }

    public function getIncomeByPlayers($from = null, $until = null): Collection
    {
        if ($from == null)
            $from = now()->subYears(10);

        if ($until == null)
            $until = now();

        $players = DB::table('carts')
            //->where('virtual_price', 0) // Only real money
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereBetween('payments.created_at', [$from, $until])
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->select(
                'users.id as id',
                'users.username',
                'users.avatar as image',
                'payments.id as payment_id',
                'payments.price as payment_price',
                'payments.currency as payment_currency'
            )
            ->get();

        $system_currency = Currency::query()->where('name', Setting::find(1)->currency)->first();

        $playersGrouped = $players->groupBy('id')->map(function ($group) use ($system_currency) {
            $user = $group->first();

            $updatedPayments = $group->map(function ($payment) {
                return [
                    'id' => $payment->payment_id,
                    'price' => $payment->payment_price,
                    'currency' => $payment->payment_currency
                ];
            })->toArray();

            foreach ($updatedPayments as $key => $payment) {
                if ($payment['currency'] != $system_currency->name) {
                    $currencyRate = Currency::query()->where('name', $payment['currency'])->first();
                    $currency = $system_currency->name;
                    if ($currencyRate) {
                        $updatedPayments[$key]['price'] = round($this->toActualCurrency($payment['price'], $system_currency->value, $currencyRate->value), 2);
                        $updatedPayments[$key]['currency'] = $system_currency->name;
                    } else {
                        $currency = Setting::find(1)->virtual_currency;
                    }
                }
            }

            // Convert array to collection
            $updatedPayments = collect($updatedPayments);

            $totalValue = CurrencyHelper::formatMoney($updatedPayments->sum('price'));
            $totalRecords = $updatedPayments->count();

            return [
                'id' => $user->id,
                'username' => $user->username,
                'image' => 'https://mc-heads.net/avatar/' . $user->username . '/30.png',
                'total_value' => $totalValue,
                'total_records' => $totalRecords,
                'currency' => $currency ?? $system_currency->name
            ];
        });

        return $playersGrouped->sortByDesc('total_value')->take(9);
    }

    public function getTopCategories($dates = null): Collection
    {
        $topCategories = DB::table('carts')
            ->join('cart_items', 'carts.id', '=', 'cart_items.cart_id')
            ->join('items', 'cart_items.item_id', '=', 'items.id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->join('payments', 'payments.cart_id', '=', 'carts.id');

        if (!is_null($dates)) {
            $topCategories = $topCategories->whereBetween('payments.created_at', [$dates['from'], $dates['until']]);
        } else {
            $topCategories = $topCategories->where('payments.created_at', '<=', now());
        }

        $topCategories = $topCategories->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->where('carts.virtual_price', 0)
            ->select(
                'categories.name',
                'categories.id as id',
                'categories.img',
                DB::raw('SUM(cart_items.price * cart_items.count) as total_value'),
                DB::raw('COUNT(DISTINCT cart_items.id) as total_records')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.img')
            ->orderBy('total_records', 'DESC')
            ->orderBy('total_value', 'DESC')
            ->limit(5)
            ->get();

        $topCategories->each(function ($category) {
            $category->total_records = CurrencyHelper::formatMoney($category->total_records);
            $category->total_value = CurrencyHelper::formatMoney($category->total_value);
        });

        return $topCategories;
    }

    public function getTopServers($dates = null): Collection
    {
        $system_currency = Currency::query()->where('name', Setting::find(1)->currency)->first();

        $topServers = DB::table('payments')
            ->join('carts', 'carts.id', '=', 'payments.cart_id')
            ->join('commands_history', 'commands_history.payment_id', '=', 'payments.id')
            ->join('servers', 'servers.id', '=', 'commands_history.server_id')
            ->where('carts.virtual_price', 0);

        if (!is_null($dates)) {
            $topServers = $topServers->whereBetween('carts.created_at', [$dates['from'], $dates['until']]);
        } else {
            $topServers = $topServers->where('carts.created_at', '<=', now());
        }

        $topServers = $topServers->select(
            'servers.name as server_name',
            'servers.id as id',
            'payments.id as payment_id',
            'payments.price as payment_price',
            'payments.currency as payment_currency'
        )
            ->distinct('payments.id')
            ->get();

        $serversGrouped = $topServers->groupBy('id')->map(function ($group) use ($system_currency) {
            $server = $group->first();

            $updatedPayments = $group->map(function ($payment) use ($system_currency) {
                if ($payment->payment_currency != $system_currency->name) {
                    $currencyRate = Currency::query()->where('name', $payment->payment_currency)->first();
                    $convertedPrice = round($this->toActualCurrency($payment->payment_price, $system_currency->value, $currencyRate->value), 2);
                    return [
                        'price' => $convertedPrice,
                        'currency' => $system_currency->name
                    ];
                } else {
                    return [
                        'price' => $payment->payment_price,
                        'currency' => $payment->payment_currency
                    ];
                }
            })->toArray();

            $updatedPayments = collect($updatedPayments);

            $totalValue = $updatedPayments->sum('price');
            $totalRecords = $updatedPayments->count();

            return [
                'name' => $server->server_name,
                'total_value' => CurrencyHelper::formatMoney($totalValue),
                'total_records' => $totalRecords
            ];
        });

        return $serversGrouped->sortByDesc('total_value')->take(5);
    }

    public function toActualCurrency($price, $currency_value, $system_currency_value)
    {
        return round(($price * $currency_value) / $system_currency_value, 2);
    }
}
