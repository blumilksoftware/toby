<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\Slack;

use Illuminate\Support\Carbon;
use Toby\Domain\DailySummaryRetriever;
use Toby\Eloquent\Models\DailySummary;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class GenerateDailySummaryAction
{
    public function __construct(
        protected DailySummaryRetriever $dailySummaryRetriever,
    ) {}

    public function execute(Carbon $day): DailySummary
    {
        /** @var DailySummary $dailySummary */
        $dailySummary = DailySummary::query()->updateOrCreate(["day" => $day->toDateString()], [
            "absences" => $this->dailySummaryRetriever->getAbsences($day)->map(fn(VacationRequest $request) => [
                "id" => $request->user->id,
                "name" => $request->user->profile->fullName,
            ])->toArray(),
            "remotes" => $this->dailySummaryRetriever->getRemoteDays($day)->map(fn(VacationRequest $request) => [
                "id" => $request->user->id,
                "name" => $request->user->profile->fullName,
            ])->toArray(),
            "birthdays" => $this->dailySummaryRetriever->getUpcomingBirthdays()->map(fn(User $user) => [
                "id" => $user->id,
                "name" => $user->profile->fullName,
                "when" => $user->upcomingBirthday()->toDateTimeString(),
            ])->toArray(),
        ]);

        return $dailySummary;
    }
}
