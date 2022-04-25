<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Handlers\BaseHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Slack\Traits\ListsHandlers;

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

        return $this->respondToSlack(":x: Nie rozpoznaję tej komendy. Lista wszystkich komend:")
            ->withAttachment(
                Attachment::create()
                    ->setColor("danger")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}