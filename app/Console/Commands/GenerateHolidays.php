<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Models\Holiday;

class GenerateHolidays extends Command
{
    protected $signature = "toby:holidays:generate {year?}";
    protected $description = "Generate default holidays for year";

    public function handle(PolishHolidaysRetriever $polishHolidaysRetriever): void
    {
        $year = (int)$this->argument("year") ?? Carbon::now()->year;

        $holidays = $polishHolidaysRetriever->getForYear($year);

        foreach ($holidays as $holiday) {
            Holiday::query()
                ->updateOrCreate([
                    "name" => $holiday["name"],
                    "date" => $holiday["date"],
                ]);
        }
    }
}
