<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Toby\Domain\DailySummaryRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Infrastructure\Slack\Elements\AbsencesAttachment;
use Toby\Infrastructure\Slack\Elements\BirthdaysAttachment;
use Toby\Infrastructure\Slack\Elements\RemotesAttachment;

class SendDailySummaryToSlack extends Command
{
    protected $signature = "toby:slack:daily-summary {--f|force}";
    protected $description = "Sent daily summary to Slack";

    public function handle(DailySummaryRetriever $dailySummaryRetriever): void
    {
        $now = Carbon::today();

        if (!$this->option("force") && !$this->shouldHandle($now)) {
            return;
        }

        $attachments = new Collection([
            new AbsencesAttachment($dailySummaryRetriever->getAbsences($now)),
            new RemotesAttachment($dailySummaryRetriever->getRemoteDays($now)),
            new BirthdaysAttachment($dailySummaryRetriever->getUpcomingBirthdays()),
        ]);

        Http::withToken($this->getSlackClientToken())
            ->post($this->getUrl(), [
                "channel" => $this->getSlackChannel(),
                "text" => __("Daily summary for day :day", ["day" => $now->toDisplayString()]),
                "attachments" => $attachments,
            ]);
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

    protected function getUrl(): string
    {
        return "{$this->getSlackBaseUrl()}/chat.postMessage";
    }

    protected function getSlackBaseUrl(): ?string
    {
        return config("services.slack.url");
    }

    protected function getSlackClientToken(): ?string
    {
        return config("services.slack.client_token");
    }

    protected function getSlackChannel(): ?string
    {
        return config("services.slack.default_channel");
    }
}
