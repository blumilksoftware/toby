<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\DailySummaryRetriever;
use Toby\Domain\DashboardAggregator;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Helpers\YearPeriodRetriever;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        UserVacationStatsRetriever $vacationStatsRetriever,
        VacationTypeConfigRetriever $configRetriever,
        DailySummaryRetriever $dailySummaryRetriever,
        DashboardAggregator $dashboardAggregator,
    ): Response {
        $user = $request->user();
        $yearPeriod = $yearPeriodRetriever->selected();

        return inertia("Dashboard", [
            "current" => $dashboardAggregator->aggregateCurrentData(),
            "upcoming" => $dashboardAggregator->aggregateUpcomingData(),
            "vacationRequests" => $dashboardAggregator->aggregateVacationRequests($user, $yearPeriod),
            "benefits" => $dashboardAggregator->aggregateUserBenefits($user),
            "calendar" => $dashboardAggregator->aggregateCalendarData($user, $yearPeriod),
            "stats" => $dashboardAggregator->aggregateStats($user, $yearPeriod),
        ]);
    }
}
