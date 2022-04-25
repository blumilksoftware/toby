<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Handlers;

use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Eloquent\Models\Key;

class KeyList extends SignatureHandler
{
    protected $signature = "toby klucze";
    protected $description = "Lista wszystkich kluczy";

    public function handle(Request $request): Response
    {
        $keys = Key::query()
            ->orderBy("id")
            ->get()
            ->map(fn(Key $key) => "Klucz nr {$key->id} - <@{$key->user->profile->slack_id}>");

        return $this->respondToSlack("Lista kluczy :key:")
            ->withAttachment(
                Attachment::create()
                    ->setColor("#3C5F97")
                    ->setText($keys->isNotEmpty() ? $keys->implode("\n") : "Nie ma żadnych kluczy w tobym"),
            );
    }
}