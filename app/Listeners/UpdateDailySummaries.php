<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Toby\Actions\Slack\UpdateDailySummaryAction;
use Toby\Actions\Slack\UpdateDailySummaryMessageAction;
use Toby\Events\VacationRequestChanged;
use Toby\Jobs\UpdateDailySummary;
use Toby\Models\DailySummary;

class UpdateDailySummaries
{
    public function __construct(
        protected UpdateDailySummaryMessageAction $updateDailySummaryMessage,
        protected UpdateDailySummaryAction $updateDailySummary,
    ) {}

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
