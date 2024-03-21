<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\User;

class EmployeesMilestonesRetriever
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getResults(?string $searchText, ?string $sort): Collection
    {
        return match ($sort) {
            "birthday-asc" => $this->getUpcomingBirthdays($searchText),
            "birthday-desc" => $this->getUpcomingBirthdays($searchText, "desc"),
            "seniority-asc" => $this->getSeniority($searchText),
            "seniority-desc" => $this->getSeniority($searchText, "desc"),
            default => User::query()
                ->search($searchText)
                ->orderByProfileField("last_name")
                ->orderByProfileField("first_name")
                ->get(),
        };
    }

    public function getUpcomingBirthdays(?string $searchText, string $direction = "asc"): Collection
    {
        $users = User::query()
            ->search($searchText)
            ->get();

        return $users->sortBy(fn(User $user): int => (int)$user->upcomingBirthday()->diffInDays(Carbon::today()), descending: $direction !== "asc");
    }

    public function getSeniority(?string $searchText, string $direction = "asc"): Collection
    {
        return User::query()
            ->search($searchText)
            ->orderByProfileField("employment_date", $direction)
            ->get();
    }
}
