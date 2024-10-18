<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Enums\Month;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;

class MonthlyUsageController extends Controller
{
    public function __invoke(
        Request $request,
        UserVacationStatsRetriever $statsRetriever,
    ): Response {
        $this->authorize("listMonthlyUsage");

        $year = $request->integer("year", Carbon::now()->year);
        $currentUser = $request->user();

        $users = User::query()
            ->withTrashed($currentUser->canSeeInactiveUsers())
            ->withVacationLimitIn($year)
            ->where("id", "!=", $currentUser->id)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        if ($currentUser->hasVacationLimit($year)) {
            $users->prepend($currentUser);
        }

        $monthlyUsage = [];

        foreach ($users as $user) {
            $vacationsByMonth = $statsRetriever->getUsedVacationDaysByMonth($user, $year);
            $limit = $statsRetriever->getVacationDaysLimit($user, $year);
            $used = $statsRetriever->getUsedVacationDays($user, $year);
            $pending = $statsRetriever->getPendingVacationDays($user, $year);
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
            "year" => $year,
            "monthlyUsage" => $monthlyUsage,
            "currentMonth" => Month::current(),
        ]);
    }
}
