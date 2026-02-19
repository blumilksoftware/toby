<?php

declare(strict_types=1);

namespace Toby\Actions\Slack;

use Toby\Domain\DailySummaryRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Models\DailySummary;
use Toby\Models\User;
use Toby\Models\VacationRequest;

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
                    "pending" => $request->state->equals(...VacationRequestStatesRetriever::pendingStates()),
                ],
            )->toArray(),
            "remotes" => $this->dailySummaryRetriever->getRemoteDays($dailySummary->day)->map(
                fn(VacationRequest $request) => [
                    "id" => $request->user->id,
                    "name" => $request->user->profile->fullName,
                    "pending" => $request->state->equals(...VacationRequestStatesRetriever::pendingStates()),
                ],
            )->toArray(),
            "birthdays" => $this->dailySummaryRetriever->getUpcomingBirthdays($dailySummary->day)->map(fn(User $user) => [
                "id" => $user->id,
                "name" => $user->profile->fullName,
                "when" => $user->upcomingBirthday($dailySummary->day)->toDateTimeString(),
            ])->toArray(),
        ]);

        return $dailySummary;
    }
}
