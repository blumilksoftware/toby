<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class DailySummaryRetriever
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    /**
     * @return Collection<VacationRequest>
     */
    public function getAbsences(Carbon $date): Collection
    {
        return VacationRequest::query()
            ->with(["user.profile", "vacations"])
            ->whereDate("from", "<=", $date)
            ->whereDate("to", ">=", $date)
            ->states(VacationRequestStatesRetriever::notFailedStates())
            ->whereIn(
                "type",
                VacationType::all()->filter(fn(VacationType $type): bool => $this->configRetriever->isVacation($type)),
            )
            ->get()
            ->sortBy("user.profile.last_name");
    }

    /**
     * @return Collection<VacationRequest>
     */
    public function getRemoteDays(Carbon $date): Collection
    {
        return VacationRequest::query()
            ->with(["user.profile", "vacations"])
            ->whereDate("from", "<=", $date)
            ->whereDate("to", ">=", $date)
            ->states(VacationRequestStatesRetriever::notFailedStates())
            ->whereIn(
                "type",
                VacationType::all()->filter(fn(VacationType $type): bool => !$this->configRetriever->isVacation($type)),
            )
            ->get()
            ->sortBy("user.profile.last_name");
    }

    /**
     * @return Collection<VacationRequest>
     */
    public function getUpcomingAbsences(Carbon $date): Collection
    {
        return VacationRequest::query()
            ->with(["user", "vacations"])
            ->whereDate("from", ">", $date)
            ->states(VacationRequestStatesRetriever::notFailedStates())
            ->whereIn(
                "type",
                VacationType::all()->filter(fn(VacationType $type): bool => $this->configRetriever->isVacation($type)),
            )
            ->orderBy("from")
            ->limit(3)
            ->get();
    }

    /**
     * @return Collection<VacationRequest>
     */
    public function getUpcomingRemoteDays(Carbon $date): Collection
    {
        return VacationRequest::query()
            ->with(["user.profile", "vacations"])
            ->whereDate("from", ">", $date)
            ->states(VacationRequestStatesRetriever::notFailedStates())
            ->whereIn(
                "type",
                VacationType::all()->filter(fn(VacationType $type): bool => !$this->configRetriever->isVacation($type)),
            )
            ->orderBy("from")
            ->limit(3)
            ->get();
    }

    /**
     * @return Collection<User>
     */
    public function getUpcomingBirthdays(?int $limit = null): Collection
    {
        $users = User::query()
            ->whereRelation("profile", fn(Builder $query): Builder => $query->whereNotNull("birthday"))
            ->get()
            ->sortBy(fn(User $user): int => (int)$user->upcomingBirthday()->diffInDays(Carbon::today()));

        if ($limit) {
            return $users->take($limit);
        }

        return $users;
    }
}
