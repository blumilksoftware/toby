<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Resources\BirthdayResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;
use Toby\Infrastructure\Http\Resources\SimpleVacationRequestResource;
use Toby\Infrastructure\Http\Resources\UserBenefitsResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

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
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->approved()
            ->get()
            ->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
                ],
            );

        $pendingVacations = $user
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->pending()
            ->get()
            ->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
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

        return VacationRequestResource::collection($vacationRequests);
    }

    public function aggregateCurrentData(): array
    {
        $today = Carbon::today();

        $absences = $this->dailySummaryRetriever->getAbsences($today);
        $remoteDays = $this->dailySummaryRetriever->getRemoteDays($today);

        return [
            "absences" => VacationRequestResource::collection($absences),
            "remoteDays" => VacationRequestResource::collection($remoteDays),
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
            "absences" => VacationRequestResource::collection($upcomingAbsences),
            "remoteDays" => VacationRequestResource::collection($upcomingRemoteDays),
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
