<?php

declare(strict_types=1);

namespace Toby\Slack\Handlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Slack\Elements\Attachment;
use Toby\Slack\Traits\ListsHandlers;

class Help extends SignatureHandler
{
    use ListsHandlers;

    protected $signature = "toby pomoc";
    protected $description = "Show all available commands";

    public function handle(Request $request): Response
    {
        $handlers = $this->findAvailableHandlers();

        $attachmentFields = $this->mapHandlersToAttachments($handlers);

        return $this->respondToSlack(__("Available commands:"))
            ->withAttachment(
                Attachment::create()
                    ->setColor("good")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}
