<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GeoHelper;
use App\Helpers\Statistics\MainHelper;
use App\Helpers\Statistics\TopListHelper;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Statistics'));
        $this->loadSettings();
    }

    public function index(Request $request, MainHelper $mainHelper, TopListHelper $listHelper): View|RedirectResponse
    {
        if (!UsersController::hasRule('statistics', 'read')) {
            return redirect('/admin');
        }

        $weeklyVisits = $mainHelper->getWeeklyVisits();
        $today = $mainHelper->getTodaySales();
        $weekly = $mainHelper->getWeeklyRevenue();
        $monthly = $mainHelper->getMonthlyRevenue();
        $global = $mainHelper->getGlobalReport();
        $total = $mainHelper->getTotalRevenue(now()->subYear()->year);
        $topPackages = $listHelper->getTopPackages();
        $topCountries = $listHelper->getSalesByCountries();
        $topPlayers = $listHelper->getIncomeByPlayers();
        $topCategories = $listHelper->getTopCategories();
        $topServers = $listHelper->getTopServers();
        $yearsRevenue = DB::table('payments')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year')
            ->get();

        return view('admin.statistics.index', compact(
            'weeklyVisits', 'today', 'weekly', 'monthly',
            'global', 'total', 'topPackages', 'topCountries', 'topPlayers', 'topServers', 'topCategories', 'yearsRevenue',
        ));
    }

    public function getByFilter(Request $request, MainHelper $mainHelper, TopListHelper $listHelper): View|RedirectResponse
    {
        if (!UsersController::hasRule('statistics', 'read')) {
            return redirect('/admin');
        }

        $date = Carbon::now()->startOfDay();

        $fromDate = '2020-01-01 00:00';
        $untilDate = $date->format('Y-m-d H:i');

        // Check for custom date range inputs
        if ($request->has('from') && $request->has('until')) {
            $fromDate = $request->input('from');
            $untilDate = $request->input('until');
        } else {
            $fromDate = match ($request->filter) {
                'month' => Carbon::now()->subMonth()->format('Y-m-d H:i'),
                'year' => Carbon::now()->subYear()->format('Y-m-d H:i'),
                '28-days' => Carbon::now()->subDays(28)->format('Y-m-d H:i'),
                default => Carbon::now()->startOfYear()->format('Y-m-d H:i'),
            };
        }

        $weeklyVisits = false;
        $between = $mainHelper->getBetweenSales($fromDate, $untilDate);
        $today = false;
        $weekly = false;
        $monthly = false;
        $global = $mainHelper->getGlobalBetweenReport($fromDate, $untilDate);
        $total = $mainHelper->getTotalRevenue(now()->subYear()->year);
        $topPackages = $listHelper->getTopPackages(['from' => $fromDate, 'until' => $untilDate]);
        $topCountries = $listHelper->getSalesByCountriesBetween(['from' => $fromDate, 'until' => $untilDate]);
        $topPlayers = $listHelper->getIncomeByPlayers($fromDate, $untilDate);
        $topCategories = $listHelper->getTopCategories(['from' => $fromDate, 'until' => $untilDate]);
        $topServers = $listHelper->getTopServers(['from' => $fromDate, 'until' => $untilDate]);
        $yearsRevenue = DB::table('payments')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->groupBy('year')
            ->get();

        return view('admin.statistics.index', compact(
            'weeklyVisits', 'today', 'between', 'weekly', 'monthly',
            'global', 'total', 'topPackages', 'topCountries', 'topPlayers', 'topServers', 'topCategories', 'yearsRevenue',
        ));
    }
}
