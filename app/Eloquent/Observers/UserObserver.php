<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class UserObserver
{
    public function __construct(
        protected Hasher $hash,
    ) {}

    public function creating(User $user): void
    {
        /**
         * A random password for user is generated because AuthenticateSession middleware needs a user's password
         * for some checks. Users use Google to login, so they don't need to know the password (GitHub issue #84)
         */
        $user->password = $this->hash->make(Str::random(40));
    }

    public function created(User $user): void
    {
        $yearPeriods = YearPeriod::all();

        foreach ($yearPeriods as $yearPeriod) {
            $user->vacationLimits()->create([
                "year_period_id" => $yearPeriod->id,
            ]);
        }
    }
}
