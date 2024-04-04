<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Enums\Month;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;

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
            ->withTrashed()
            ->withVacationLimitIn($currentYearPeriod)
            ->where("id", "!=", $currentUser->id)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
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
                "user" => new SimpleUserResource($user),
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
        ]);
    }
}
