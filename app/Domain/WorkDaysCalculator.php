<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Toby\Enums\VacationType;
use Toby\Models\Holiday;

class WorkDaysCalculator
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function calculateDays(CarbonInterface $from, CarbonInterface $to, ?VacationType $vacationType = null): Collection
    {
        $period = CarbonPeriod::create($from, $to);
        $holidays = $this->getHolidays($from->year);

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

    protected function getHolidays(int $year): Collection
    {
        return Holiday::query()
            ->whereYear("date", $year)
            ->pluck("date");
    }
}
