<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Handlers;

use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Slack\Elements\KeysAttachment;

class KeyList extends SignatureHandler
{
    protected $signature = "toby klucze";
    protected $description = "Lista wszystkich kluczy";

    public function handle(Request $request): Response
    {
        $keys = Key::query()
            ->orderBy("id")
            ->get();

        return $this->respondToSlack("Lista kluczy :key:")
            ->withAttachment(new KeysAttachment($keys));
    }
}
