<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Toby\Domain\Actions\Slack\UpdateDailySummaryAction;
use Toby\Domain\Actions\Slack\UpdateDailySummaryMessageAction;
use Toby\Eloquent\Models\DailySummary;

class UpdateDailySummary implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(protected DailySummary $dailySummary)
    {
    }

    public function handle(UpdateDailySummaryMessageAction $updateDailySummaryMessage, UpdateDailySummaryAction $updateDailySummary): void
    {
        $updateDailySummaryMessage->execute($updateDailySummary->execute($this->dailySummary));
    }
}
