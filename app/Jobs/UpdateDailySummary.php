<?php

declare(strict_types=1);

namespace Toby\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Toby\Actions\Slack\UpdateDailySummaryAction;
use Toby\Actions\Slack\UpdateDailySummaryMessageAction;
use Toby\Models\DailySummary;

class UpdateDailySummary implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        protected DailySummary $dailySummary,
    ) {}

    public function handle(UpdateDailySummaryMessageAction $updateDailySummaryMessage, UpdateDailySummaryAction $updateDailySummary): void
    {
        $updateDailySummaryMessage->execute($updateDailySummary->execute($this->dailySummary));
    }
}
