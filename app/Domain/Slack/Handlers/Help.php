<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Slack\Traits\ListsHandlers;

class Help extends SignatureHandler
{
    use ListsHandlers;

    protected $signature = "toby pomoc";
    protected $description = "Wyświetl wszystkie dostępne komendy";

    public function handle(Request $request): Response
    {
        $handlers = $this->findAvailableHandlers();

        $attachmentFields = $this->mapHandlersToAttachments($handlers);

        return $this->respondToSlack("Dostępne komendy:")
            ->withAttachment(
                Attachment::create()
                    ->setColor("good")
                    ->useMarkdown()
                    ->setFields($attachmentFields),
            );
    }
}