<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\SlashCommand\Attachment as BaseAttachment;

class Attachment extends BaseAttachment implements Arrayable
{
}