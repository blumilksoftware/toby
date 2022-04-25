<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Exceptions;

use Spatie\SlashCommand\Exceptions\SlackSlashCommandException;

class UserNotFoundException extends SlackSlashCommandException
{
}