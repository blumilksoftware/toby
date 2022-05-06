<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenGivenNotification;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Slack\Exceptions\UserNotFoundException;
use Toby\Infrastructure\Slack\Rules\SlackUserExistsRule;
use Toby\Infrastructure\Slack\Traits\FindsUserBySlackId;

class GiveKeysTo extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:dla {user}";
    protected $description = "Give the keys to the specified user";

    /**
     * @throws UserNotFoundException
     * @throws ValidationException
     */
    public function handle(Request $request): Response
    {
        ["user" => $from] = $this->validate();

        $authUser = $this->findUserBySlackIdOrFail($request->userId);
        $user = $this->findUserBySlackId($from);

        /** @var Key $key */
        $key = $authUser->keys()->first();

        if (!$key) {
            throw ValidationException::withMessages(["key" => __("You don't have any key to give")]);
        }

        if ($user->is($authUser)) {
            throw ValidationException::withMessages([
                "key" => __("You can't give the keys to yourself :dzban:"),
            ]);
        }

        $key->user()->associate($user);

        $key->save();

        $key->notify(new KeyHasBeenGivenNotification($authUser, $user));

        return $this->respondToSlack(
            __(":white_check_mark: Key no. :key has been given to <@:user>", ["key"=>$key->id, "user" => $user->profile->slack_id])
        );
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
            "user.required" => "You must specified the user to whom you want to give the keys",
        ];
    }
}
