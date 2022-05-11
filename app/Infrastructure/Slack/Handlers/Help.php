<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Infrastructure\Slack\Elements\Attachment;
use Toby\Infrastructure\Slack\Traits\ListsHandlers;

class Help extends SignatureHandler
{
    use ListsHandlers;

    protected $signature = "toby pomoc";
    protected $description = "Wyświetl wszystkie dostępne polecenia";

    public function handle(Request $request): Response
    {
        $handlers = $this->findAvailableHandlers();

        $attachmentFields = $this->mapHandlersToAttachments($handlers);

        return $this->respondToSlack("Dostępne polecenia:")
            ->withAttachment(
                Attachment::create()
                    ->setColor("good")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}
