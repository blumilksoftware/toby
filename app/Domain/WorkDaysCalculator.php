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

    public function calculateDays(CarbonInterface $from, CarbonInterface $to, ?VacationType $vacationType = null): Collection
    {
        $period = CarbonPeriod::create($from, $to);
        $yearPeriod = YearPeriod::findByYear($from->year);
        $holidays = $yearPeriod->holidays()->pluck("date");

        $validDays = new Collection();

        foreach ($period as $day) {
            if ($this->passes($day, $holidays, $vacationType)) {
                $validDays->add($day);
            }
        }

        return $validDays;
    }

    protected function passes(CarbonInterface $day, Collection $holidays, ?VacationType $vacationType = null): bool
    {
        if ($vacationType && $this->configRetriever->isDuringNonWorkDays($vacationType)) {
            return true;
        }

        if ($day->isWeekend()) {
            return false;
        }

        if ($holidays->contains($day)) {
            return false;
        }

        return true;
    }
}
