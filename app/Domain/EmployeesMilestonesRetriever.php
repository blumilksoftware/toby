<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Models\UserHistory;

class EmployeesMilestonesRetriever
{
    protected bool $hasPermissionToViewInactiveUsers;

    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getResults(User $user, ?string $searchText, ?string $sort): Collection
    {
        $this->hasPermissionToViewInactiveUsers = $user->hasPermissionTo("showInactiveUsers");

        return match ($sort) {
            "birthday-asc" => $this->getUpcomingBirthdays($searchText),
            "birthday-desc" => $this->getUpcomingBirthdays($searchText, "desc"),
            "seniority-asc" => $this->getSeniority($searchText),
            "seniority-desc" => $this->getSeniority($searchText, "desc"),
            default => User::query()
                ->withTrashed($this->hasPermissionToViewInactiveUsers)
                ->search($searchText)
                ->orderByProfileField("last_name")
                ->orderByProfileField("first_name")
                ->get(),
        };
    }

    public function getUpcomingBirthdays(?string $searchText, string $direction = "asc"): Collection
    {
        $users = User::query()
            ->withTrashed($this->hasPermissionToViewInactiveUsers)
            ->search($searchText)
            ->get();

        return $users->sortBy(fn(User $user): int => (int)$user->upcomingBirthday()->diffInDays(Carbon::today()), descending: $direction !== "desc");
    }

    public function getSeniority(?string $searchText, string $direction = "asc"): Collection
    {
        return User::query()
            ->withTrashed($this->hasPermissionToViewInactiveUsers)
            ->search($searchText)
            ->orderBy(
                UserHistory::query()
                    ->select("from")
                    ->whereColumn("users.id", "user_histories.user_id")
                    ->where("type", UserHistoryType::Employment)
                    ->where("is_employed_at_current_company", true)
                    ->orderBy("from", $direction)
                    ->limit(1),
                $direction,
            )
            ->get();
    }
}
