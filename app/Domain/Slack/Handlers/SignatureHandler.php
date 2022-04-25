<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Facades\Validator;
use Spatie\SlashCommand\Handlers\SignatureHandler as BaseSignatureHandler;

abstract class SignatureHandler extends BaseSignatureHandler
{
    public function validate()
    {
        return Validator::validate($this->getArguments(), $this->getRules(), $this->getMessages());
    }

    protected function getRules(): array
    {
        return [];
    }

    protected function getMessages(): array
    {
        return [];
    }
}