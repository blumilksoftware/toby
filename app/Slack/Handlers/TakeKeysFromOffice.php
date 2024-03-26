<?php

declare(strict_types=1);

namespace Toby\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenTakenFromTheOfficeNotification;
use Toby\Models\Key;
use Toby\Slack\Exceptions\UserNotFoundException;
use Toby\Slack\Traits\FindsUserBySlackId;

class TakeKeysFromOffice extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:biuro:wez";
    protected $description = "Take the keys from the office.";

    /**
     * @throws UserNotFoundException
     * @throws ValidationException
     */
    public function handle(Request $request): Response
    {
        $authUser = $this->findUserBySlackIdOrFail($request->userId);

        /** @var Key|null $key */
        $key = Key::query()->whereNull("user_id")->first();

        if (!$key) {
            throw ValidationException::withMessages(["key" => __("There are no keys in the office.")]);
        }

        $key->user()->associate($authUser);

        $key->save();

        $key->notify(new KeyHasBeenTakenFromTheOfficeNotification($authUser));

        return $this->respondToSlack(
            __(":white_check_mark: Key no. :number has been taken from the office", ["number" => $key->id]),
        );
    }
}
