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
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Resources\BirthdayResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;
use Toby\Infrastructure\Http\Resources\SimpleVacationRequestResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

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
        $birthdays = $dailySummaryRetriever->getBirthdays($now);
        $upcomingAbsences = $dailySummaryRetriever->getUpcomingAbsences($now);
        $upcomingRemoteDays = $dailySummaryRetriever->getUpcomingRemoteDays($now);
        $upcomingBirthdays = $dailySummaryRetriever->getUpcomingBirthdays();

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

        $approvedVacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->approved()
            ->get();

        $pendingVacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->pending()
            ->get();

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $hasLimit = $vacationStatsRetriever->hasVacationDaysLimit($user, $yearPeriod);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $remoteWork = $vacationStatsRetriever->getRemoteWorkDays($user, $yearPeriod);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);
        $remaining = $limit - $used - $pending;

        return inertia("Dashboard", [
            "absences" => VacationRequestResource::collection($absences),
            "remoteDays" => VacationRequestResource::collection($remoteDays),
            "birthdays" => BirthdayResource::collection($birthdays),
            "upcomingAbsences" => VacationRequestResource::collection($upcomingAbsences),
            "upcomingRemoteDays" => VacationRequestResource::collection($upcomingRemoteDays),
            "vacationRequests" => VacationRequestResource::collection($vacationRequests),
            "upcomingBirthdays" => BirthdayResource::collection($upcomingBirthdays),
            "holidays" => HolidayResource::collection($holidays),
            "allHolidays" => $allHolidays->mapWithKeys(
                fn(Holiday $holiday): array => [$holiday->date->toDateString() => $holiday->name],
            ),
            "approvedVacations" => $approvedVacations->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
                ],
            ),
            "pendingVacations" => $pendingVacations->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
                ],
            ),
            "stats" => [
                "hasLimit" => $hasLimit,
                "limit" => $limit,
                "remaining" => $remaining,
                "used" => $used,
                "pending" => $pending,
                "remoteWork" => $remoteWork,
                "other" => $other,
            ],
            "can" => [
                "listAllVacationRequests" => $user->can("listAll", VacationRequest::class),
            ],
        ]);
    }
}
