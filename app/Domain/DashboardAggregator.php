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
use Toby\Models\YearPeriod;

class DashboardAggregator
{
    public function __construct(
        protected DailySummaryRetriever $dailySummaryRetriever,
        protected UserVacationStatsRetriever $vacationStatsRetriever,
        protected UserBenefitsRetriever $benefitsRetriever,
    ) {}

    public function aggregateStats(User $user, YearPeriod $yearPeriod): array
    {
        $limit = $this->vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $hasLimit = $this->vacationStatsRetriever->hasVacationDaysLimit($user, $yearPeriod);
        $used = $this->vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $this->vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $remoteWork = $this->vacationStatsRetriever->getRemoteWorkDays($user, $yearPeriod);
        $other = $this->vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);
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

    public function aggregateCalendarData(User $user, YearPeriod $yearPeriod): array
    {
        $approvedVacations = $user
            ->vacations()
            ->with(["vacationRequest.vacations", "vacationRequest.user.profile"])
            ->whereBelongsTo($yearPeriod)
            ->cache("vacations{$user->id}")
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
            ->whereBelongsTo($yearPeriod)
            ->cache("vacations{$user->id}")
            ->pending()
            ->get()
            ->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new DashboardVacationRequestResource($vacation->vacationRequest),
                ],
            );

        $holidays = $yearPeriod
            ->holidays
            ->mapWithKeys(fn(Holiday $holiday): array => [$holiday->date->toDateString() => $holiday->name]);

        return [
            "approvedVacations" => $approvedVacations,
            "pendingVacations" => $pendingVacations,
            "holidays" => $holidays,
        ];
    }

    public function aggregateVacationRequests(User $user, YearPeriod $yearPeriod): JsonResource
    {
        if ($user->can("listAllRequests")) {
            $vacationRequests = $yearPeriod->vacationRequests()
                ->with(["user", "vacations", "vacations.user", "vacations.user.profile", "user.permissions", "user.profile"])
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
                ->latest("updated_at")
                ->limit(3)
                ->get();
        } else {
            $vacationRequests = $user->vacationRequests()
                ->with(["user", "vacations", "vacations.user", "vacations.user.profile", "user.permissions", "user.profile"])
                ->whereBelongsTo($yearPeriod)
                ->latest("updated_at")
                ->limit(3)
                ->get();
        }

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
        $upcomingBirthdays = $this->dailySummaryRetriever->getUpcomingBirthdays(3);

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
