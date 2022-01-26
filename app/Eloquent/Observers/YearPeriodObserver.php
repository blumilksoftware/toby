<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Domain\PolishHolidaysRetriever;
use Toby\Eloquent\Helpers\UserAvatarGenerator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class YearPeriodObserver
{
    public function __construct(
        protected UserAvatarGenerator $generator,
        protected PolishHolidaysRetriever $polishHolidaysRetriever,
    ) {
    }

    public function created(YearPeriod $yearPeriod): void
    {
        $this->createVacationLimitsFor($yearPeriod);
        $this->createHolidaysFor($yearPeriod);
    }

    protected function createVacationLimitsFor(YearPeriod $yearPeriod): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $yearPeriod->vacationLimits()->create([
                "user_id" => $user->id,
            ]);
        }
    }

    protected function createHolidaysFor(YearPeriod $yearPeriod): void
    {
        $holidays = $this->polishHolidaysRetriever->getForYearPeriod($yearPeriod);

        foreach ($holidays as $holiday) {
            $yearPeriod->holidays()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }
    }
}
