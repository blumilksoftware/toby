<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Collection;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\AttachmentField;
use Spatie\SlashCommand\Handlers\Help as BaseHelpHandler;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;

class Help extends BaseHelpHandler
{
    protected $signature = "toby pomoc";
    protected $description = "Wyświetl wszystkie dostępne komendy tobiego";

    public function handle(Request $request): Response
    {
        $handlers = $this->findAvailableHandlers();

        return $this->displayListOfAllCommands($handlers);
    }

    protected function displayListOfAllCommands(Collection $handlers): Response
    {
        $attachmentFields = $handlers
            ->sort(function (SignatureHandler $handlerA, SignatureHandler $handlerB) {
                return strcmp($handlerA->getFullCommand(), $handlerB->getFullCommand());
            })
            ->map(function (SignatureHandler $handler) {
                return AttachmentField::create("/{$handler->getSignature()}", $handler->getDescription());
            })
            ->all();

        return $this->respondToSlack('Dostępne komendy')
            ->withAttachment(
                Attachment::create()
                    ->setColor('good')
                    ->setFields($attachmentFields)
            );
    }
}