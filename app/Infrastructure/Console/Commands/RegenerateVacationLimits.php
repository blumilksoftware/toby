<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Eloquent\Models\VacationLimit;

class RegenerateVacationLimits extends Command
{
    protected $signature = "toby:regenerate-vacation-limits";
    protected $description = "Regenerates vacation limits to new schema";

    public function handle(): void
    {
        VacationLimit::all()->each(
            fn(VacationLimit $limit): bool => $limit->update([
                "limit" => $limit->from_previous_year + $limit->days - $limit->to_next_year,
            ]),
        );
    }
}
