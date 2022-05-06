<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenTakenNotification;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Slack\Exceptions\UserNotFoundException;
use Toby\Infrastructure\Slack\Rules\SlackUserExistsRule;
use Toby\Infrastructure\Slack\Traits\FindsUserBySlackId;

class TakeKeysFrom extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:od {user}";
    protected $description = "Take keys from specified user";

    /**
     * @throws UserNotFoundException|ValidationException
     */
    public function handle(Request $request): Response
    {
        ["user" => $from] = $this->validate();

        $authUser = $this->findUserBySlackIdOrFail($request->userId);
        $user = $this->findUserBySlackId($from);

        /** @var Key $key */
        $key = $user->keys()->first();

        if (!$key) {
            throw ValidationException::withMessages([
                "key" => __("User <@:user> does not have any keys", ["user" => $user->profile->slack_id])
            ]);
        }

        if ($key->user()->is($authUser)) {
            throw ValidationException::withMessages([
                "key" => __("You can't take the keys from yourself :dzban:"),
            ]);
        }

        $key->user()->associate($authUser);

        $key->save();

        $key->notify(new KeyHasBeenTakenNotification($authUser, $user));

        return $this->respondToSlack(__(":white_check_mark: Key no. :key has been taken from user <@:user>", ["key"=>$key->id, "user"=> $user->profile->slack_id]));
    }

    protected function getRules(): array
    {
        return [
            "user" => ["required", new SlackUserExistsRule()],
        ];
    }

    protected function getMessages(): array
    {
        return [
            "user.required" => __("You must specified the user you want to take the keys from")
        ];
    }
}
