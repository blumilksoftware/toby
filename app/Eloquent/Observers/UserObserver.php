<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->password = Hash::make(Str::random(40));
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
