<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;

class DailySummaryRetriever
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getAbsences(Carbon $date): Collection
    {
        return Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", $date)
            ->approved()
            ->whereTypes(
                VacationType::all()->filter(fn(VacationType $type): bool => $this->configRetriever->isVacation($type)),
            )
            ->get()
            ->sortBy("user.last_name");
    }

    public function getRemoteDays(Carbon $date): Collection
    {
        return Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", $date)
            ->approved()
            ->whereTypes(
                VacationType::all()->filter(fn(VacationType $type): bool => !$this->configRetriever->isVacation($type)),
            )
            ->get()
            ->sortBy("user.last_name");
    }

    public function getUpcomingAbsences(Carbon $date): Collection
    {
        return Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", ">", $date)
            ->approved()
            ->whereTypes(
                VacationType::all()->filter(fn(VacationType $type): bool => $this->configRetriever->isVacation($type)),
            )
            ->limit(3)
            ->get();
    }

    public function getUpcomingRemoteDays(Carbon $date): Collection
    {
        return Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", ">", $date)
            ->approved()
            ->whereTypes(
                VacationType::all()->filter(fn(VacationType $type): bool => !$this->configRetriever->isVacation($type)),
            )
            ->limit(3)
            ->get();
    }

    public function getBirthdays(Carbon $date): Collection
    {
        return User::query()
            ->whereRelation("profile", "birthday", $date)
            ->get();
    }
}
