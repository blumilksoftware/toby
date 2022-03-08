<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class UserObserver
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {}

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
