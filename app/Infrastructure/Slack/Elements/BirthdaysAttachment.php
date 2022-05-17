<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Elements;

use Illuminate\Support\Collection;
use Toby\Eloquent\Models\User;

class BirthdaysAttachment extends ListAttachment
{
    public function __construct(Collection $birthdays)
    {
        parent::__construct();

        $this
            ->setTitle(__("Birthdays :birthday:"))
            ->setColor("#3c5f97")
            ->setItems($birthdays->map(fn(User $user): string => $user->profile->full_name))
            ->setEmptyText(__("Nobody has a birthday today :cry:"));
    }
}
