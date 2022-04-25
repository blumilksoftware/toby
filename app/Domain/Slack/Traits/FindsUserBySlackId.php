<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Traits;

use Illuminate\Support\Str;
use Toby\Domain\Slack\UserNotFoundException;
use Toby\Eloquent\Models\User;

trait FindsUserBySlackId
{
    protected function findUserBySlackId(string $slackId): ?User
    {
        $id = $this->prepareSlackIdFromString($slackId);

        /** @var User $user */
        $user = User::query()
            ->whereRelation("profile", "slack_id", $id)
            ->first();

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    protected function findUserBySlackIdOrFail(string $slackId): ?User
    {
        $user = $this->findUserBySlackId($slackId);

        if (!$user) {
            throw new UserNotFoundException("Użytkownik {$slackId} nie istnieje w tobym");
        }

        return $user;
    }

    protected function prepareSlackIdFromString(string $slackId): string
    {
        return Str::between($slackId, "<@", "|");
    }
}