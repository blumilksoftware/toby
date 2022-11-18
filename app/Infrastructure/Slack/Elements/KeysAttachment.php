<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\Key;

class KeysAttachment extends ListAttachment
{
    public function __construct(Collection $keys)
    {
        parent::__construct();

        $this
            ->setColor("#3c5f97")
            ->setItems($keys->map(function (Key $key): string {
                if ($key->user === null) {
                    return "Klucz nr {$key->id} - jest w biurze";
                }

                if ($key->user->profile->slack_id === null) {
                    return "Klucz nr {$key->id} - {$key->user->profile->full_name}";
                }

                return "Klucz nr {$key->id} - <@{$key->user->profile->slack_id}>";
            }))
            ->setEmptyText(__("There are no keys in toby"));
    }
}
