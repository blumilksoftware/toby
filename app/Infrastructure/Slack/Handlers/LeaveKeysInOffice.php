<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenLeftInTheOffice;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Slack\Exceptions\UserNotFoundException;
use Toby\Infrastructure\Slack\Traits\FindsUserBySlackId;

class LeaveKeysInOffice extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:biuro:zostaw";
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
            throw ValidationException::withMessages(["key" => __("You don't have any key to leave in the office.")]);
        }

        $key->user()->associate(null);

        $key->save();

        $key->notify(new KeyHasBeenLeftInTheOffice($authUser));

        return $this->respondToSlack(
            __(":white_check_mark: Key no. :number has been left in the office", ["number" => $key->id]),
        );
    }
}
