<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;

class SlackMessage
{
    protected string $text = "";
    protected Collection $attachments;

    public function __construct()
    {
        $this->attachments = new Collection();
    }

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function withAttachment(Attachment $attachment): static
    {
        $this->attachments->push($attachment);

        return $this;
    }

    public function withAttachments(Collection $attachments): static
    {
        foreach ($attachments as $attachment) {
            $this->withAttachment($attachment);
        }

        return $this;
    }

    public function getPayload(): array
    {
        return [
            "text" => $this->text,
            "link_names" => true,
            "unfurl_links" => true,
            "unfurl_media" => true,
            "mrkdwn" => true,
            "attachments" => $this->attachments->toArray(),
        ];
    }
}
