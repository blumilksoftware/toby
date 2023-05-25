<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\Slack;

use Toby\Domain\DailySummaryRetriever;
use Toby\Eloquent\Models\DailySummary;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class UpdateDailySummaryAction
{
    public function __construct(
        protected DailySummaryRetriever $dailySummaryRetriever,
    ) {}

    public function execute(DailySummary $dailySummary): DailySummary
    {
        $dailySummary->update([
            "absences" => $this->dailySummaryRetriever->getAbsences($dailySummary->day)->map(
                fn(VacationRequest $request) => [
                    "id" => $request->user->id,
                    "name" => $request->user->profile->fullName,
                ],
            )->toArray(),
            "remotes" => $this->dailySummaryRetriever->getRemoteDays($dailySummary->day)->map(
                fn(VacationRequest $request) => [
                    "id" => $request->user->id,
                    "name" => $request->user->profile->fullName,
                ],
            )->toArray(),
            "birthdays" => $this->dailySummaryRetriever->getUpcomingBirthdays()->map(fn(User $user) => [
                "id" => $user->id,
                "name" => $user->profile->fullName,
                "when" => $user->upcomingBirthday()->toDateTimeString(),
            ])->toArray(),
        ]);

        return $dailySummary;
    }
}
