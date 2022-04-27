<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Spatie\SlashCommand\Handlers\BaseHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Infrastructure\Slack\Elements\Attachment;
use Toby\Infrastructure\Slack\Traits\ListsHandlers;

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

        return $this->respondToSlack(":x: Nie rozpoznaję polecenia. Lista wszystkich poleceń:")
            ->withAttachment(
                Attachment::create()
                    ->setColor("danger")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}
