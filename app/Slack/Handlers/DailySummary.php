<?php

declare(strict_types=1);

namespace Toby\Slack\Handlers;

use Illuminate\Support\Carbon;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Actions\Slack\GenerateDailySummaryAction;

class DailySummary extends SignatureHandler
{
    protected $signature = "toby dzisiaj";
    protected $description = "Daily summary";

    public function handle(Request $request): Response
    {
        $generateDailySummary = app()->make(GenerateDailySummaryAction::class);

        $dailySummary = $generateDailySummary->execute(Carbon::now());

        return $this->respondToSlack($dailySummary->getTitle())
            ->withAttachments($dailySummary->getAttachments()->flatten()->all());
    }
}
