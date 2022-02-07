<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\YearPeriod;

class VacationDaysCalculator
{
    public function calculateDays(YearPeriod $yearPeriod, CarbonInterface $from, CarbonInterface $to): Collection
    {
        $period = CarbonPeriod::create($from, $to);
        $holidays = $yearPeriod->holidays()->pluck("date");

        $validDays = new Collection();

        foreach ($period as $day) {
            if ($this->passes($day, $holidays)) {
                $validDays->add($day);
            }
        }

        return $validDays;
    }

    protected function passes(CarbonInterface $day, Collection $holidays): bool
    {
        if ($day->isWeekend()) {
            return false;
        }

        if ($holidays->contains($day)) {
            return false;
        }

        return true;
    }
}
