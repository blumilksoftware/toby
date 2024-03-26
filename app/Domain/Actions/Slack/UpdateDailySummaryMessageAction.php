<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\Slack;

use Illuminate\Support\Facades\Http;
use Toby\Models\DailySummary;

class UpdateDailySummaryMessageAction
{
    public function execute(DailySummary $dailySummary): DailySummary
    {
        Http::withToken($this->getSlackClientToken())
            ->post($this->getUrl(), [
                "channel" => $dailySummary->channel_id,
                "ts" => $dailySummary->message_id,
                "text" => $dailySummary->getTitle(),
                "attachments" => $dailySummary->getAttachments(),
            ]);

        return $dailySummary;
    }

    protected function getUrl(): string
    {
        return "{$this->getSlackBaseUrl()}/chat.update";
    }

    protected function getSlackBaseUrl(): ?string
    {
        return config("services.slack.url");
    }

    protected function getSlackClientToken(): ?string
    {
        return config("services.slack.client_token");
    }
}
