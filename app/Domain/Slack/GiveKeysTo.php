<?php

declare(strict_types=1);

namespace Toby\Domain\Slack;

use Illuminate\Support\Str;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

class GiveKeysTo extends SignatureHandler
{
    protected $signature = "toby klucze:dla {to}";

    protected $description = "Daj klucze użytkownikowi {to}";

    public function handle(Request $request): Response
    {
        $to = $this->getArgument('to');

        $id = Str::between($to, "@", "|");

        $authUser = $this->findUserBySlackId($request->userId);
        $user = $this->findUserBySlackId($id);

        /** @var Key $key */
        $key = $authUser->keys()->first();

        $key->user()->associate($user);

        $key->save();

        return $this->respondToSlack("<@{$authUser->profile->slack_id}> daje klucz nr {$key->id} użytkownikowi <@{$user->profile->slack_id}>")
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