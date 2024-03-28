<?php

declare(strict_types=1);

namespace Toby\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Models\Key;

class KeysAttachment extends ListAttachment
{
    public function __construct(Collection $keys)
    {
        parent::__construct();

        $this
            ->setColor("#3c5f97")
            ->setItems($keys->map(function (Key $key): string {
                if ($key->user === null) {
                    return __("Key no. :number - is in the office.", [
                        "number" => $key->id,
                    ]);
                }

                if ($key->user->profile->slack_id === null) {
                    return __("Key no. :number - :user", [
                        "number" => $key->id,
                        "user" => $key->user->profile->full_name,
                    ]);
                }

                return __("Key no. :number - :user", [
                    "number" => $key->id,
                    "user" => "<@{$key->user->profile->slack_id}>",
                ]);
            }))
            ->setEmptyText(__("There are no keys in toby"));
    }
}
