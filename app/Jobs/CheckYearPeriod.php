<?php

declare(strict_types=1);

namespace Toby\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Toby\Models\YearPeriod;

class CheckYearPeriod implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function handle(): void
    {
        $currentYearPeriod = YearPeriod::current();

        if ($currentYearPeriod === null) {
            $this->createCurrentYearPeriod();
        }

        if (YearPeriod::query()->max("year") === Carbon::now()->year) {
            $this->createNextYearPeriod();
        }
    }

    protected function createCurrentYearPeriod(): void
    {
        YearPeriod::query()->create([
            "year" => Carbon::now()->year,
        ]);
    }

    protected function createNextYearPeriod(): void
    {
        YearPeriod::query()->create([
            "year" => Carbon::now()->year + 1,
        ]);
    }
}
