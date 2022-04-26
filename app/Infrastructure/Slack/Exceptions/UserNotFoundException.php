<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Exceptions;

use Spatie\SlashCommand\Exceptions\SlackSlashCommandException;

class UserNotFoundException extends SlackSlashCommandException
{
}
