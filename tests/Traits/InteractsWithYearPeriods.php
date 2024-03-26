<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\YearPeriod;

trait InteractsWithYearPeriods
{
    use InteractsWithSession;

    public function createYearPeriod(int $year): YearPeriod
    {
        /** @var YearPeriod $yearPeriod */
        $yearPeriod = YearPeriod::factory()->create([
            "year" => $year,
        ]);

        return $yearPeriod;
    }

    public function createCurrentYearPeriod(): YearPeriod
    {
        return $this->createYearPeriod(Carbon::now("1")->year);
    }

    public function markYearPeriodAsSelected(YearPeriod $yearPeriod): void
    {
        $this->session([
            YearPeriodRetriever::SESSION_KEY => $yearPeriod->id,
        ]);
    }

    public function clearSelectedYearPeriod(): void
    {
        $this->session([]);
    }

    public function cleanYearPeriods(): void
    {
        $this->clearSelectedYearPeriod();

        YearPeriod::query()->delete();
    }
}
