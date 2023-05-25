<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Actions\Slack\RetrieveDailySummaryAction;

class DailySummary extends SignatureHandler
{
    protected $signature = "toby dzisiaj";
    protected $description = "Daily summary";

    public function handle(Request $request): Response
    {
        $retrieveDailySummary = app()->make(RetrieveDailySummaryAction::class);

        $dailySummary = $retrieveDailySummary->execute(Carbon::now());

        return $this->respondToSlack($dailySummary->getTitle())
            ->withAttachments($dailySummary->getAttachments());
    }
}
