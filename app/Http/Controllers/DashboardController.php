<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\DailySummaryRetriever;
use Toby\Domain\DashboardAggregator;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationTypeConfigRetriever;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        UserVacationStatsRetriever $vacationStatsRetriever,
        VacationTypeConfigRetriever $configRetriever,
        DailySummaryRetriever $dailySummaryRetriever,
        DashboardAggregator $dashboardAggregator,
    ): Response {
        $user = $request->user();
        $year = Carbon::now()->year;

        return inertia("Dashboard", [
            "current" => $dashboardAggregator->aggregateCurrentData(),
            "upcoming" => $dashboardAggregator->aggregateUpcomingData(),
            "vacationRequests" => $dashboardAggregator->aggregateVacationRequests($user, $year),
            "benefits" => $dashboardAggregator->aggregateUserBenefits($user),
            "calendar" => $dashboardAggregator->aggregateCalendarData($user, $year),
            "stats" => $dashboardAggregator->aggregateStats($user, $year),
        ]);
    }
}
