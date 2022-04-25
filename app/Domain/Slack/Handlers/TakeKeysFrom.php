<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Domain\Notifications\KeyHasBeenTakenNotification;
use Toby\Domain\Slack\Exceptions\UserNotFoundException;
use Toby\Domain\Slack\Rules\SlackUserExistsRule;
use Toby\Domain\Slack\Traits\FindsUserBySlackId;
use Toby\Eloquent\Models\Key;

class TakeKeysFrom extends SignatureHandler
{
    use FindsUserBySlackId;

    protected $signature = "toby klucze:od {user}";
    protected $description = "Zabierz klucze wskazanemu użytkownikowi";

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
                "key" => "Użytkownik <@{$user->profile->slack_id}> nie ma żadnych kluczy",
            ]);
        }

        if ($key->user()->is($authUser)) {
            throw ValidationException::withMessages([
                "key" => "Nie możesz zabrać sobie kluczy :dzban:",
            ]);
        }

        $key->user()->associate($authUser);

        $key->save();

        $key->notify(new KeyHasBeenTakenNotification($authUser, $user));

        return $this->respondToSlack(":white_check_mark: Klucz nr {$key->id} został zabrany użytkownikowi <@{$user->profile->slack_id}>");
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
            "user.required" => "Musisz podać użytkownika, któremu chcesz zabrać klucze",
        ];
    }
}