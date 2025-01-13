<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\DashboardAggregator;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardAggregator $dashboardAggregator): Response
    {
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

    public function loadCalendar(Request $request, int $year, DashboardAggregator $dashboardAggregator): array
    {
        return $dashboardAggregator->aggregateCalendarData($request->user(), $year);
    }
}
