<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Resources\AbsenceResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

class DashboardController extends Controller
{
    public function __invoke(Request $request, UserVacationStatsRetriever $vacationStatsRetriever): Response
    {
        $user = $request->user();
        $now = Carbon::now();
        $yearPeriod = YearPeriod::findByYear($now->year);

        $absences = Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", $now)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query->states(VacationRequestStatesRetriever::successStates()),
            )
            ->get();

        if ($user->can("listAll", VacationRequest::class)) {
            $vacationRequests = VacationRequest::query()
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
                ->latest("updated_at")
                ->limit(3)
                ->get();
        } else {
            $vacationRequests = $user->vacationRequests()
                ->latest("updated_at")
                ->limit(3)
                ->get();
        }

        $holidays = Holiday::query()
            ->whereDate("date", ">=", $now)
            ->latest()
            ->limit(3)
            ->get();

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);
        $remaining = $limit - $used - $pending;

        return inertia("Dashboard", [
            "absences" => AbsenceResource::collection($absences),
            "vacationRequests" => VacationRequestResource::collection($vacationRequests),
            "holidays" => HolidayResource::collection($holidays),
            "stats" => [
                "limit" => $limit,
                "remaining" => $remaining,
                "used" => $used,
                "pending" => $pending,
                "other" => $other,
            ],
            "can" => [
                "listAllVacationRequests" => $user->can("listAll", VacationRequest::class),
            ],
        ]);
    }
}
