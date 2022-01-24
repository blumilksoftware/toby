<?php

declare(strict_types=1);

namespace Toby\Observers;

use Toby\Helpers\UserAvatarGenerator;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class YearPeriodObserver
{
    public function __construct(
        protected UserAvatarGenerator $generator,
    ) {
    }

    public function created(YearPeriod $yearPeriod): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $yearPeriod->vacationLimits()->updateOrCreate(["user_id" => $user->id]);
        }
    }
}
