<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\DailySummaryRetriever;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Resources\HolidayResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;
use Toby\Infrastructure\Http\Resources\VacationResource;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        UserVacationStatsRetriever $vacationStatsRetriever,
        VacationTypeConfigRetriever $configRetriever,
        DailySummaryRetriever $dailySummaryRetriever,
    ): Response {
        $user = $request->user();
        $now = Carbon::now();
        $yearPeriod = $yearPeriodRetriever->selected();

        $absences = $dailySummaryRetriever->getAbsences($now);
        $remoteDays = $dailySummaryRetriever->getRemoteDays($now);

        if ($user->can("listAll", VacationRequest::class)) {
            $vacationRequests = $yearPeriod->vacationRequests()
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
                ->latest("updated_at")
                ->limit(3)
                ->get();
        } else {
            $vacationRequests = $user->vacationRequests()
                ->whereBelongsTo($yearPeriod)
                ->latest("updated_at")
                ->limit(3)
                ->get();
        }

        $holidays = $yearPeriod
            ->holidays()
            ->whereDate("date", ">=", $now)
            ->orderBy("date")
            ->limit(3)
            ->get();

        $allHolidays = $yearPeriod->holidays;

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $homeOffice = $vacationStatsRetriever->getHomeOfficeDays($user, $yearPeriod);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);
        $remaining = $limit - $used - $pending;

        return inertia("Dashboard", [
            "absences" => VacationResource::collection($absences),
            "remoteDays" => VacationResource::collection($remoteDays),
            "vacationRequests" => VacationRequestResource::collection($vacationRequests),
            "holidays" => HolidayResource::collection($holidays),
            "allHolidays" => $allHolidays->mapWithKeys(
                fn(Holiday $holiday): array => [$holiday->date->toDateString() => $holiday->name],
            ),
            "stats" => [
                "limit" => $limit,
                "remaining" => $remaining,
                "used" => $used,
                "pending" => $pending,
                "homeOffice" => $homeOffice,
                "other" => $other,
            ],
            "can" => [
                "listAllVacationRequests" => $user->can("listAll", VacationRequest::class),
            ],
        ]);
    }
}
