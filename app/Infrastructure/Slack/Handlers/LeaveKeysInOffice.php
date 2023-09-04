<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenGivenNotification;
use Toby\Domain\Notifications\KeyHasBeenLeftInTheOffice;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Slack\Exceptions\UserNotFoundException;
use Toby\Infrastructure\Slack\Rules\SlackUserExistsRule;
use Toby\Infrastructure\Slack\Traits\FindsUserBySlackId;

class LeaveKeysInOffice extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:biuro:daj";
    protected $description = "Leave the keys in the office.";

    /**
     * @throws UserNotFoundException
     * @throws ValidationException
     */
    public function handle(Request $request): Response
    {
        $authUser = $this->findUserBySlackIdOrFail($request->userId);

        /** @var Key $key */
        $key = $authUser->keys()->first();

        if (!$key) {
            throw ValidationException::withMessages(["key" => __("You don't have any key to give")]);
        }

        $key->user()->associate(null);

        $key->save();

        $key->notify(new KeyHasBeenLeftInTheOffice($authUser));

        return $this->respondToSlack(
            __(":white_check_mark: Key no. :key has been left in the office", ["key" => $key->id]),
        );
    }
}
