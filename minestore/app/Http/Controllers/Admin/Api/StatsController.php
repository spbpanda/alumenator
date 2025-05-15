<?php

namespace App\Http\Controllers\Admin\Api;

use App\Helpers\SortHelper;
use App\Helpers\Statistics\MainHelper;
use App\Helpers\Statistics\TopListHelper;
use App\Http\Controllers\Admin\Controller;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Requests\BanUserRequest;
use App\Http\Requests\GetTotalRevenueRequest;
use App\Models\Ban;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StatsController extends Controller
{
    /**
     * API endpoint to get top players by income
     * @param Request $request
     * @return JsonResponse
     */
    public function topPlayers(Request $request, TopListHelper $helper): JsonResponse
    {
        if (!UsersController::hasRule('statistics', 'read')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $topPlayers = match ($request->filter) {
            'month' => $helper->getIncomeByPlayers(now()->startOfMonth()),
            'year' => $helper->getIncomeByPlayers(now()->startOfYear()),
            default => $helper->getIncomeByPlayers()
        };

        return response()->json($topPlayers);
    }

    /**
     * API endpoint to get total revenue
     * @param GetTotalRevenueRequest $request
     * @param MainHelper $mainHelper
     * @return JsonResponse
     */
    public function totalRevenue(GetTotalRevenueRequest $request, MainHelper $mainHelper): JsonResponse
    {
        if (!UsersController::hasRule('statistics', 'write')) {
            return response()->json(['message' => 'Not authorized'], Response::HTTP_UNAUTHORIZED);
        }

        $year = $request->year ?? now()->subYear()->year;
        $total = $mainHelper->getTotalRevenue($year);

        return response()->json($total);
    }
}
