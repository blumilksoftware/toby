<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\DailySummaryRetriever;
use Toby\Infrastructure\Slack\Elements\AbsencesAttachment;
use Toby\Infrastructure\Slack\Elements\BirthdaysAttachment;
use Toby\Infrastructure\Slack\Elements\RemotesAttachment;

class DailySummary extends SignatureHandler
{
    protected $signature = "toby dzisiaj";
    protected $description = "Daily summary";

    public function handle(Request $request): Response
    {
        $dailySummaryRetriever = app()->make(DailySummaryRetriever::class);

        $now = Carbon::today();

        $attachments = new Collection([
            new AbsencesAttachment($dailySummaryRetriever->getAbsences($now)),
            new RemotesAttachment($dailySummaryRetriever->getRemoteDays($now)),
            new BirthdaysAttachment($dailySummaryRetriever->getUpcomingBirthdays()),
        ]);

        return $this->respondToSlack(__("Summary for the day :day", ["day" => $now->toDisplayString()]))
            ->withAttachments($attachments->all());
    }
}
