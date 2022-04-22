<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Collection;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\AttachmentField;
use Spatie\SlashCommand\Handlers\CatchAll as BaseCatchAllHandler;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;

class CatchAll extends BaseCatchAllHandler
{
    public function handle(Request $request): Response
    {
        $response = $this->respondToSlack("Nie rozpoznaję tej komendy: `/{$request->command} {$request->text}`");

        [$command] = explode(' ', $this->request->text ?? "");

        $alternativeHandlers = $this->findAlternativeHandlers($command);

        if ($alternativeHandlers->count()) {
            $response->withAttachment($this->getCommandListAttachment($alternativeHandlers));
        }

        if ($this->containsHelpHandler($alternativeHandlers)) {
            $response->withAttachment(Attachment::create()
                ->setText("Aby wyświetlić wszystkie komendy, napisz: `/toby pomoc`")
            );
        }

        return $response;
    }

    protected function getCommandListAttachment(Collection $handlers): Attachment
    {
        $attachmentFields = $handlers
            ->map(function (SignatureHandler $handler) {
                return AttachmentField::create($handler->getFullCommand(), $handler->getDescription());
            })
            ->all();

        return Attachment::create()
            ->setColor('warning')
            ->setTitle('Czy miałeś na myśli:')
            ->setFields($attachmentFields);
    }
}