<?php

declare(strict_types=1);

namespace Toby\Domain\Actions;

use Toby\Domain\PolishHolidaysRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class CreateYearPeriodAction
{
    public function __construct(
        protected PolishHolidaysRetriever $polishHolidaysRetriever,
    ) {}

    public function execute(int $year): YearPeriod
    {
        $yearPeriod = new YearPeriod([
            "year" => $year,
        ]);

        $yearPeriod->save();

        $this->createVacationLimitsFor($yearPeriod);
        $this->createHolidaysFor($yearPeriod);

        return $yearPeriod;
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
