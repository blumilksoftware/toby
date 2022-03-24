<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Enums\Month;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\UserResource;

class MonthlyUsageController extends Controller
{
    public function __invoke(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        UserVacationStatsRetriever $statsRetriever,
    ): Response {
        $this->authorize("listMonthlyUsage");

        $currentYearPeriod = $yearPeriodRetriever->selected();
        $currentUser = $request->user();

        $users = User::query()
            ->whereRelation(
                "vacationlimits",
                fn(Builder $query) => $query->where("year_period_id", $currentYearPeriod->id)->whereNotNull("days"),
            )
            ->where("id", "!=", $currentUser->id)
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        if ($currentUser->hasVacationLimit($currentYearPeriod)) {
            $users->prepend($currentUser);
        }

        $monthlyUsage = [];

        foreach ($users as $user) {
            $vacationsByMonth = $statsRetriever->getUsedVacationDaysByMonth($user, $currentYearPeriod);
            $limit = $statsRetriever->getVacationDaysLimit($user, $currentYearPeriod);
            $used = $statsRetriever->getUsedVacationDays($user, $currentYearPeriod);
            $pending = $statsRetriever->getPendingVacationDays($user, $currentYearPeriod);
            $remaining = $limit - $used - $pending;

            $monthlyUsage[] = [
                "user" => new UserResource($user),
                "months" => $vacationsByMonth,
                "stats" => [
                    "used" => $used,
                    "pending" => $pending,
                    "remaining" => $remaining,
                ],
            ];
        }

        return inertia("MonthlyUsage", [
            "monthlyUsage" => $monthlyUsage,
            "currentMonth" => Month::current(),
            "can" => [
                "listMonthlyUsage" => $request->user()->can("listMonthlyUsage"),
            ],
        ]);
    }
}
