<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Domain\Actions\Slack\GenerateDailySummaryAction;
use Toby\Domain\Actions\Slack\SendDailySummaryToSlackAction;
use Toby\Models\Holiday;

class SendDailySummaryToSlack extends Command
{
    protected $signature = "toby:slack:daily-summary {--f|force}";
    protected $description = "Sent daily summary to Slack";

    public function handle(
        GenerateDailySummaryAction $generateDailySummary,
        SendDailySummaryToSlackAction $sendDailySummary,
    ): void {
        $now = Carbon::today();

        if (!$this->option("force") && !$this->shouldHandle($now)) {
            return;
        }

        $dailySummary = $generateDailySummary->execute($now);

        $sendDailySummary->execute($dailySummary);
    }

    protected function shouldHandle(CarbonInterface $day): bool
    {
        $holidays = Holiday::query()->whereDate("date", $day)->pluck("date");

        if ($day->isWeekend()) {
            return false;
        }

        if ($holidays->contains($day)) {
            return false;
        }

        return true;
    }
}
