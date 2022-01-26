<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Eloquent\Helpers\UserAvatarGenerator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

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
            $yearPeriod->vacationLimits()->create([
                "user_id" => $user->id,
            ]);
        }
    }
}
