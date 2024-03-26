<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\Slack;

use Illuminate\Support\Facades\Http;
use Toby\Models\DailySummary;

class SendDailySummaryToSlackAction
{
    public function execute(DailySummary $dailySummary): DailySummary
    {
        $message = Http::withToken($this->getSlackClientToken())
            ->post($this->getUrl(), [
                "channel" => $this->getSlackChannel(),
                "text" => $dailySummary->getTitle(),
                "attachments" => $dailySummary->getAttachments(),
            ]);

        $data = $message->json();

        $dailySummary->update([
            "channel_id" => $data["channel"],
            "message_id" => $data["message"]["ts"],
        ]);

        return $dailySummary;
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
