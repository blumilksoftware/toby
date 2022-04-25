<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\SlashCommand\AttachmentField;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Spatie\SlashCommand\Handlers\SignatureParts;
use Spatie\SlashCommand\HandlesSlashCommand;

trait ListsHandlers
{
    protected function findAvailableHandlers(): Collection
    {
        return collect(config("laravel-slack-slash-command.handlers"))
            ->map(fn(string $handlerClassName) => new $handlerClassName($this->request))
            ->filter(fn(HandlesSlashCommand $handler) => $handler instanceof SignatureHandler)
            ->filter(function (SignatureHandler $handler) {
                $signatureParts = new SignatureParts($handler->getSignature());

                return Str::is($signatureParts->getSlashCommandName(), $this->request->command);
            });
    }

    protected function mapHandlersToAttachments(Collection $handlers): array
    {
        return $handlers
            ->sort(
                fn(SignatureHandler $handlerA, SignatureHandler $handlerB) => strcmp(
                    $handlerA->getFullCommand(),
                    $handlerB->getFullCommand(),
                ),
            )
            ->map(
                fn(SignatureHandler $handler) => AttachmentField::create(
                    $handler->getDescription(),
                    "`/{$handler->getSignature()}`",
                ),
            )
            ->all();
    }
}