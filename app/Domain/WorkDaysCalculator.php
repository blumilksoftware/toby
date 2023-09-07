<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\YearPeriod;

class WorkDaysCalculator
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function calculateDays(
        VacationType $vacationType,
        CarbonInterface $from,
        CarbonInterface $to,
    ): Collection
    {
        $period = CarbonPeriod::create($from, $to);
        $yearPeriod = YearPeriod::findByYear($from->year);
        $holidays = $yearPeriod->holidays()->pluck("date");

        $validDays = new Collection();

        foreach ($period as $day) {
            if ($this->passes($vacationType, $day, $holidays)) {
                $validDays->add($day);
            }
        }

        return $validDays;
    }

    protected function passes(VacationType $vacationType, CarbonInterface $day, Collection $holidays): bool
    {
        if ($day->isWeekend() && !$this->configRetriever->isDuringNonWorkDays($vacationType)) {
            return false;
        }

        if ($holidays->contains($day) && !$this->configRetriever->isDuringNonWorkDays($vacationType)) {
            return false;
        }

        return true;
    }
}
