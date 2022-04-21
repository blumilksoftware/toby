<?php

declare(strict_types=1);

namespace Toby\Domain\Slack;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Spatie\SlashCommand\Handlers\SignatureHandler;
use Toby\Eloquent\Models\Key;

class KeyList extends SignatureHandler
{
    protected $signature = "toby klucze";

    protected $description = "Lista wszystkich kluczy";

    public function handle(Request $request): Response
    {
        $temp = [];

        foreach (Key::orderBy("id")->get() as $key) {
            $temp[] = "Klucz nr {$key->id} - <@{$key->user->profile->slack_id}>";
        }


        return $this->respondToSlack(implode("\n", $temp))
            ->displayResponseToEveryoneOnChannel();
    }
}