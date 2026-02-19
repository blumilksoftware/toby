<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Toby\Http\Resources\BirthdayResource;
use Toby\Http\Resources\DashboardVacationRequestResource;
use Toby\Http\Resources\HolidayResource;
use Toby\Http\Resources\UserBenefitsResource;
use Toby\Http\Resources\VacationRequestResource;
use Toby\Models\Holiday;
use Toby\Models\User;
use Toby\Models\Vacation;
use Toby\Models\VacationRequest;

class DashboardAggregator
{
    public function __construct(
        protected DailySummaryRetriever $dailySummaryRetriever,
        protected UserVacationStatsRetriever $vacationStatsRetriever,
        protected UserBenefitsRetriever $benefitsRetriever,
    ) {}

    public function aggregateStats(User $user, ?int $year = null): array
    {
        $year ??= Carbon::now()->year;

        $limit = $this->vacationStatsRetriever->getVacationDaysLimit($user, $year);
        $hasLimit = $this->vacationStatsRetriever->hasVacationDaysLimit($user, $year);
        $used = $this->vacationStatsRetriever->getUsedVacationDays($user, $year);
        $pending = $this->vacationStatsRetriever->getPendingVacationDays($user, $year);
        $remoteWork = $this->vacationStatsRetriever->getRemoteWorkDays($user, $year);
        $other = $this->vacationStatsRetriever->getOtherApprovedVacationDays($user, $year);
        $remaining = $limit - $used - $pending;

        return [
            "hasLimit" => $hasLimit,
            "limit" => $limit,
            "remaining" => $remaining,
            "used" => $used,
            "pending" => $pending,
            "remoteWork" => $remoteWork,
            "other" => $other,
        ];
    }

    public function aggregateCalendarData(User $user, ?int $year = null): array
    {
        $approvedVacations = $user
            ->vacations()
            ->with(["vacationRequest.vacations", "vacationRequest.user.profile"])
            ->whereYear("date", $year ?? Carbon::now()->year)
            ->cache("vacations:{$user->id}")
            ->approved()
            ->get()
            ->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new DashboardVacationRequestResource($vacation->vacationRequest),
                ],
            );

        $pendingVacations = $user
            ->vacations()
            ->with(["vacationRequest.vacations", "vacationRequest.user.profile"])
            ->whereYear("date", $year ?? Carbon::now()->year)
            ->cache("vacations:{$user->id}")
            ->pending()
            ->get()
            ->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new DashboardVacationRequestResource($vacation->vacationRequest),
                ],
            );

        $holidays = Holiday::query()
            ->whereYear("date", $year)
            ->get()
            ->mapWithKeys(fn(Holiday $holiday): array => [$holiday->date->toDateString() => $holiday->name]);

        return [
            "approvedVacations" => $approvedVacations,
            "pendingVacations" => $pendingVacations,
            "holidays" => $holidays,
        ];
    }

    public function aggregateVacationRequests(User $user, ?int $year = null): JsonResource
    {
        $year ??= Carbon::now()->year;

        $query = $user->can("listAllRequests")
            ? VacationRequest::query()
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
            : $user->vacationRequests();

        $vacationRequests = $query
            ->with(["user", "vacations", "vacations.user", "vacations.user.profile", "user.permissions", "user.profile"])
            ->whereYear("from", $year)
            ->latest("updated_at")
            ->limit(3)
            ->get();

        return VacationRequestResource::collection($vacationRequests);
    }

    public function aggregateCurrentData(): array
    {
        $today = Carbon::today();

        $absences = $this->dailySummaryRetriever->getAbsences($today);
        $remoteDays = $this->dailySummaryRetriever->getRemoteDays($today);

        return [
            "absences" => DashboardVacationRequestResource::collection($absences),
            "remoteDays" => DashboardVacationRequestResource::collection($remoteDays),
        ];
    }

    public function aggregateUpcomingData(): array
    {
        $today = Carbon::today();

        $upcomingAbsences = $this->dailySummaryRetriever->getUpcomingAbsences($today);
        $upcomingRemoteDays = $this->dailySummaryRetriever->getUpcomingRemoteDays($today);
        $upcomingBirthdays = $this->dailySummaryRetriever->getUpcomingBirthdays($today, 3);

        $upcomingHolidays = Holiday::query()
            ->whereDate("date", ">=", $today)
            ->orderBy("date")
            ->limit(3)
            ->get();

        return [
            "absences" => DashboardVacationRequestResource::collection($upcomingAbsences),
            "remoteDays" => DashboardVacationRequestResource::collection($upcomingRemoteDays),
            "birthdays" => BirthdayResource::collection($upcomingBirthdays),
            "holidays" => HolidayResource::collection($upcomingHolidays),
        ];
    }

    public function aggregateUserBenefits(User $user): JsonResource
    {
        $benefits = $this->benefitsRetriever->getAssignedBenefits($user);

        return UserBenefitsResource::collection($benefits);
    }
}
