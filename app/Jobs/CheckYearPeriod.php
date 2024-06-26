<?php

declare(strict_types=1);

namespace Toby\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Toby\Actions\CreateYearPeriodAction;
use Toby\Models\YearPeriod;

class CheckYearPeriod implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function handle(CreateYearPeriodAction $createYearPeriodAction): void
    {
        $currentYearPeriod = YearPeriod::current();
        $now = Carbon::now();

        if ($currentYearPeriod === null) {
            $createYearPeriodAction->execute($now->year);
        }

        if (YearPeriod::query()->max("year") === $now->year) {
            $createYearPeriodAction->execute($now->year + 1);
        }
    }
}
