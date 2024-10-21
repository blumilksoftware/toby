<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateYearPeriodYearToVacationLimits extends Command
{
    protected $signature = "toby:migrate-year-period-year-to-vacation-limits";
    protected $description = "Migrate year period to vacation limits";

    public function handle(): void
    {
        DB::statement("
            UPDATE vacation_limits
            SET year = year_periods.year
            FROM year_periods
            WHERE year_periods.id = vacation_limits.year_period_id
        ");
    }
}
