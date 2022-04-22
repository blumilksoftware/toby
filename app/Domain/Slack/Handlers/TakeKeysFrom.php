<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Illuminate\Support\Str;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

class TakeKeysFrom extends SignatureHandler
{
    protected $signature = "toby klucze:od {użytkownik}";

    protected $description = "Zabierz klucze wskazanemu użytkownikowi";

    public function handle(Request $request): Response
    {
        $from = $this->getArgument("użytkownik");

        $id = Str::between($from, "@", "|");

        $authUser = $this->findUserBySlackId($request->userId);
        $user = $this->findUserBySlackId($id);

        /** @var Key $key */
        $key = $user->keys()->first();

        $key->user()->associate($authUser);

        $key->save();

        return $this->respondToSlack("<@{$authUser->profile->slack_id}> zabiera klucz nr {$key->id} użytkownikowi <@{$user->profile->slack_id}>")
            ->displayResponseToEveryoneOnChannel();
    }

    protected function findUserBySlackId(string $slackId): User
    {
        /** @var User $user */
        $user = User::query()
            ->whereRelation("profile", "slack_id", $slackId)
            ->first();

        return $user;
    }
}