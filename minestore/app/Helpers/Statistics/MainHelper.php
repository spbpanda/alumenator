<?php

namespace App\Helpers\Statistics;

use App\Helpers\CurrencyHelper;
use App\Helpers\ValuesDifferenceHelper;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SiteVisit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Support\Collection;

class MainHelper
{
    public function getWeeklyVisits(): object
    {
        $visits = SiteVisit::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $visitsCount = CurrencyHelper::formatMoney($visits->sum('count'));

        $period = now()->startOfWeek()->daysUntil(now()->endOfWeek());

        $prevWeek = $this->getPrevWeekVisits();

        $difference = ValuesDifferenceHelper::getPercentageDifference($visitsCount, $prevWeek->amount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        return (object)[
            'amount' => $visitsCount,
            'chartData' => $this->weeklyVisitsChartData($visits, $period),
            'difference' => $difference,
            'level' => $level
        ];
    }

    /**
     * Get statistic data for today income
     * @return object
     */
    public function getTodaySales(): object
    {
        // Get today income
        $todayAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereDate('payments.created_at', today())
            ->sum('carts.price');

        // Get yesterday income
        $yesterdayAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereDate('payments.created_at', today()->subDay())
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($todayAmount, $yesterdayAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        $formattedAmount = CurrencyHelper::formatMoney($todayAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level
        ];
    }

    /**
     * Get statistic data for range income
     * @return object
     */
    public function getBetweenSales($fromDate, $untilDate): object
    {
        $todayAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$fromDate, $untilDate])
            ->sum('carts.price');

        $formattedAmount = CurrencyHelper::formatMoney($todayAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => false,
            'level' => false
        ];
    }

    /**
     * Get statistic data for the week income
     * @return object
     */
    public function getWeeklyRevenue(): object
    {
        // Get this week income
        $thisWeekAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('carts.price');

        // Get previous week income
        $previousWeekAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($thisWeekAmount, $previousWeekAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        // Format money
        $formattedAmount = CurrencyHelper::formatMoney($thisWeekAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level
        ];
    }

    /**
     * Get statistic data for the month income
     * @return object
     */
    public function getMonthlyRevenue(): object
    {
        // Get this month income
        $thisMonthAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('carts.price');

        // Get previous month income
        $previousMonthAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($thisMonthAmount, $previousMonthAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        $formattedAmount = CurrencyHelper::formatMoney($thisMonthAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level
        ];
    }

    /**
     * Get statistic data for the month income for dashboard homepage
     * @return object
     */
    public function getMonthlyRevenueHomepage(): object
    {
        // Get this month income
        $thisMonthAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->startOfMonth(), now()])
            ->sum('carts.price');

        // Get previous month income
        $previousMonthAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()])
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($thisMonthAmount, $previousMonthAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        $formattedAmount = CurrencyHelper::formatMoney($thisMonthAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level
        ];
    }

    /**
     * Get statistic data for the year income
     * @return object
     */
    public function getGlobalReport(): object
    {
        // Get this month income
        $thisYearAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->startOfYear(), now()->endOfYear()])
            ->sum('carts.price');

        // Get previous month income
        $previousYearAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($thisYearAmount, $previousYearAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        // Format money
        $formattedAmount = CurrencyHelper::formatMoney($thisYearAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level,
            'year' => today()->year,
            'chartData' => $this->globalReportChartData()
        ];
    }

    /**
     * Get statistic data for the year income
     * @return object
     */
    public function getGlobalBetweenReport($fromDate, $untilDate): object
    {
        // Get this range income
        $thisRangeAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$fromDate, $untilDate])
            ->sum('carts.price');

        // Get previous range income
        $carbonFromDate = Carbon::parse($fromDate);
        $carbonUntilDate = Carbon::parse($untilDate);
        $carbonBetweenSeconds = abs($carbonUntilDate->diffInSeconds($carbonFromDate));
        $previousRangeAmount = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$carbonFromDate->subSeconds($carbonBetweenSeconds), $carbonUntilDate->subSeconds($carbonBetweenSeconds)])
            ->sum('carts.price');

        // Calculate difference in percentage
        $difference = ValuesDifferenceHelper::getPercentageDifference($thisRangeAmount, $previousRangeAmount);
        $level = ($difference == 0) ? 'equal' : ($difference > 0 ? 'up' : 'down');

        // Format money
        $formattedAmount = CurrencyHelper::formatMoney($thisRangeAmount);

        return (object)[
            'amount' => $formattedAmount,
            'difference' => $difference,
            'level' => $level,
            'year' => false,
            'chartData' => $this->globalReportChartData(['from' => $fromDate, 'until' => $untilDate]),
        ];
    }

    public function getTotalRevenue(int $year): object
    {
        $currentYear = $this->totalRevenueChartData(now()->year);
        $previousYear = $this->totalRevenueChartData($year);

        $difference = ValuesDifferenceHelper::getPercentageDifference($currentYear->sum, $previousYear->sum);

        return (object)[
            'difference' => $difference,
            'currentYear' => $currentYear,
            'previousYear' => $previousYear
        ];
    }

    private function globalReportChartData($dates = null): object
    {
        // Get the sum of cart prices by month
        $cartSumByMonth = Cart::query()->join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR]);

        // If dates are not provided, use the current year period
        if (is_null($dates)) {
            $cartSumByMonth = $cartSumByMonth->whereBetween('payments.created_at', [now()->startOfYear(), now()]);
        } else {
            $cartSumByMonth = $cartSumByMonth->whereBetween('payments.created_at', [$dates['from'], $dates['until']]);
        }

        // Group payments by month and calculate the sum for each month
        $system_currency = Currency::find(Setting::find(1)->currency);
        $currencies = Currency::all()->keyBy('name');

        $virtual_currency = Setting::find(1)->virtual_currency;

        $cartSumByMonth = $cartSumByMonth->get()
            ->groupBy(fn($c) => $c->created_at->format('Y-m'))
            ->map(function ($arr) use ($system_currency, $currencies, $virtual_currency) {
                $total = collect($arr)->reduce(function ($carry, $payment) use ($system_currency, $currencies, $virtual_currency) {
                    if ($payment->currency != $system_currency->name && $payment->currency != $virtual_currency) {
                        $currencyRate = $currencies[$payment->currency];
                        $convertedPrice = round($this->toActualCurrency($payment->price, $system_currency->value, $currencyRate->value), 2);
                        return $carry + $convertedPrice;
                    } else {
                        return $carry + $payment->price;
                    }
                }, 0);

                return round($total, 2);
            });

        // Generate month names and initialize an array with 0 if value is null
        $period = now()->startOfYear()->monthsUntil(now());
        if (!is_null($dates)) {
            $period = Carbon::parse($dates['from'])->startOfMonth()->monthsUntil(Carbon::parse($dates['until'])->endOfMonth());
        }

        $categories = [];
        $zeroArray = new Collection;
        foreach ($period as $date) {
            $categories[] = $date->shortMonthName;
            $zeroArray->put($date->format('Y-m'), 0);
        }

        // Determine the last month in the period
        $lastMonth = Carbon::parse(end($period));
        $lastMonthNumber = $lastMonth->month;

        // Add remaining months (if any) from the last month to December
        $currentYear = $lastMonth->year;
        for ($month = $lastMonthNumber + 1; $month <= 12; $month++) {
            $monthDate = Carbon::create($currentYear, $month, 1);
            $categories[] = $monthDate->shortMonthName;
            $zeroArray->put($monthDate->format('Y-m'), 0);
        }

        // Merge the data with the empty zero array
        $data = $zeroArray->merge($cartSumByMonth)->values()->toArray();

        return (object)[
            'categories' => $categories,
            'data' => $data
        ];
    }

    private function totalRevenueChartData(int $year): object
    {
        $date = Carbon::createFromDate($year, 1, 1);
        $end = $year == now()->year ? now() : $date->endOfYear();

        $currencies = Currency::all()->keyBy('name');
        $system_currency = $currencies[Setting::find(1)->currency];

        $payments = DB::table('payments')
            ->join('carts', 'carts.id', '=', 'payments.cart_id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$date, $end])
            ->select('payments.price as payment_price', 'payments.currency as payment_currency', 'carts.created_at')
            ->get();

        $virtual_currency = Setting::find(1)->virtual_currency;
        $cartSumByMonth = $payments->groupBy(fn($p) => Carbon::parse($p->created_at)->format('Y-m'))
            ->map(function ($arr) use ($system_currency, $currencies, $virtual_currency) {
                $total = collect($arr)->reduce(function ($carry, $payment) use ($system_currency, $currencies, $virtual_currency) {
                    if ($payment->payment_currency != $system_currency->name && $payment->payment_currency != $virtual_currency) {
                        $currencyRate = $currencies[$payment->payment_currency];
                        $convertedPrice = round($this->toActualCurrency($payment->payment_price, $system_currency->value, $currencyRate->value), 2);
                        return $carry + $convertedPrice;
                    } else {
                        return $carry + $payment->payment_price;
                    }
                }, 0);

                return round($total, 2);
            });

        $period = now()->startOfYear()->monthsUntil(now()->endOfYear());
        $categories = [];
        $zeroArray = new Collection;
        foreach ($period as $date) {
            $categories[] = $date->shortMonthName;
            $zeroArray->put($date->format('Y-m'), 0);
        }

        $data = $zeroArray->merge($cartSumByMonth)->values()->toArray();
        $sum = array_sum($data);

        return (object)[
            'name' => $year,
            'categories' => $categories,
            'data' => $data,
            'sum' => $sum,
            'formatted_sum' => CurrencyHelper::formatMoney($sum)
        ];
    }

    private function weeklyVisitsChartData(Collection $visits, CarbonPeriod $period): object
    {
        // Generate day names
        $categories = [];
        $data = new Collection;

        // Generate day names
        foreach ($period as $date) {
            $shortDayName = date('D', $date->getTimestamp())[0];
            $categories[] = $shortDayName;
            $data->put($date->format('Y-m-d'), 0);
        }

        // Update array with visit count
        foreach ($visits as $visit) {
            $data[$visit->created_at->format('Y-m-d')] = $visit->count;
        }

        return (object)[
            'categories' => $categories,
            'data' => $data->values()
        ];
    }

    public function formatSalesData(Collection $carts, CarbonPeriod $period): object
    {
        // Generate day names
        $categories = [];
        $data = new Collection;
        foreach ($period as $date) {
            $categories[] = $date->day;
            $data->put($date->format('Y-m-d'), 0);
        }

        // Update array with visit count
        foreach ($carts as $cart) {
            $data[Carbon::parse($cart->created_at)->format('Y-m-d')] = round($cart->price, 2);
        }

        return (object)[
            'categories' => $categories,
            'data' => $data->values()
        ];
    }

    /**
     * Get month revenue
     * @param int $month
     * @return string
     */
    public function getRevenueByMonth(int $month): string
    {
        $start = now()->setMonth($month)->startOfMonth();
        $end = now()->setMonth($month)->endOfMonth();

        // Get month income
        $monthRevenue = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$start, $end])
            ->sum('carts.price');

        return CurrencyHelper::formatMoney($monthRevenue);
    }

    public function getPrevWeekVisits(): object
    {
        $visits = SiteVisit::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->get();
        $visitsCount = CurrencyHelper::formatMoney($visits->sum('count'));

        $period = now()->subWeek()->startOfWeek()->daysUntil(now()->subWeek()->endOfWeek());

        return (object)[
            'amount' => $visitsCount,
            'chartData' => $this->weeklyVisitsChartData($visits, $period)
        ];
    }

    public function getDataBetweenDates(string $fromDate, string $untilDate): object
    {
        // Convert date strings to Carbon instances
        $fromDate = Carbon::createFromFormat('Y-m-d H:i', $fromDate)->startOfDay();
        $untilDate = Carbon::createFromFormat('Y-m-d H:i', $untilDate)->endOfDay();

        // Get data between specified dates
        $data = Cart::join('payments', 'payments.cart_id', '=', 'carts.id')
            ->whereIn('payments.status', [Payment::PAID, Payment::COMPLETED, Payment::ERROR])
            ->whereBetween('payments.created_at', [$fromDate, $untilDate])
            ->get();

        // Process and format the data as needed
        $totalAmount = $data->sum('carts.price');
        $formattedTotalAmount = CurrencyHelper::formatMoney($totalAmount);

        // Additional details
        $averageAmount = $data->count() > 0 ? CurrencyHelper::formatMoney($totalAmount / $data->count()) : CurrencyHelper::formatMoney(0);
        $maxAmount = $data->max('carts.price');
        $minAmount = $data->min('carts.price');

        return (object)[
            'totalAmount' => $formattedTotalAmount,
            'averageAmount' => $averageAmount,
            'maxAmount' => CurrencyHelper::formatMoney($maxAmount),
            'minAmount' => CurrencyHelper::formatMoney($minAmount),
            'details' => $data, // Include more details if needed
        ];
    }

    public function toActualCurrency($price, $currency_value, $system_currency_value)
    {
        return round(($price * $currency_value) / $system_currency_value, 2);
    }
}
