<?php

declare(strict_types=1);

namespace Toby\Observers;

use Toby\Helpers\PolishHolidaysRetriever;
use Toby\Helpers\UserAvatarGenerator;
use Toby\Models\User;
use Toby\Models\YearPeriod;

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
