<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Traits;

use Illuminate\Support\Str;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Slack\Exceptions\UserNotFoundException;

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
            throw new UserNotFoundException(__("User :input does not exist in toby", ["input"=> $slackId]));
        }

        return $user;
    }

    protected function prepareSlackIdFromString(string $slackId): string
    {
        return Str::between($slackId, "<@", "|");
    }
}
