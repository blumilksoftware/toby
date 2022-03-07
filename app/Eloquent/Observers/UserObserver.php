<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;

class UserObserver
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {}

    public function created(User $user): void
    {
        $user->vacationLimits()->create([
            "year_period_id" => $this->yearPeriodRetriever->current()->id,
        ]);
    }
}
