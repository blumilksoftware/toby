<?php

declare(strict_types=1);

namespace Toby\Slack\Handlers;

use Spatie\SlashCommand\Handlers\BaseHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Slack\Elements\Attachment;
use Toby\Slack\Traits\ListsHandlers;

class CatchAll extends BaseHandler
{
    use ListsHandlers;

    public function canHandle(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): Response
    {
        $handlers = $this->findAvailableHandlers();
        $attachmentFields = $this->mapHandlersToAttachments($handlers);

        return $this->respondToSlack(__(":x: I don't recognize the command. List of all commands:"))
            ->withAttachment(
                Attachment::create()
                    ->setColor("danger")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}
