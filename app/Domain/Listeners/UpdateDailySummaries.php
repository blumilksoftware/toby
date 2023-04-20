<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Actions\Slack\UpdateDailySummaryAction;
use Toby\Domain\Actions\Slack\UpdateDailySummaryMessageAction;
use Toby\Domain\Events\VacationRequestChanged;
use Toby\Eloquent\Models\DailySummary;
use Toby\Infrastructure\Jobs\UpdateDailySummary;

class UpdateDailySummaries
{
    public function __construct(
        protected UpdateDailySummaryMessageAction $updateDailySummaryMessage,
        protected UpdateDailySummaryAction $updateDailySummary
    ) {
    }

    public function handle(VacationRequestChanged $event): void
    {
        $period = [$event->vacationRequest->from->toDateString(), $event->vacationRequest->to->toDateString()];

        $dailySummaries = DailySummary::query()
            ->whereBetween("day", $period)
            ->whereNotNull("message_id")
            ->latest("day")
            ->get();

        foreach ($dailySummaries as $dailySummary) {
            UpdateDailySummary::dispatch($dailySummary);
        }
    }
}
