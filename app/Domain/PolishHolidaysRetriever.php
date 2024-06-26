<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Models\YearPeriod;
use Yasumi\Holiday;
use Yasumi\Yasumi;

class PolishHolidaysRetriever
{
    protected const string PROVIDER_KEY = "Poland";
    protected const string LANG_KEY = "pl";

    public function getForYearPeriod(YearPeriod $yearPeriod): Collection
    {
        $polishProvider = Yasumi::create(static::PROVIDER_KEY, $yearPeriod->year);

        $holidays = $polishProvider->getHolidays();

        return $this->prepareHolidays($holidays);
    }

    protected function prepareHolidays(array $holidays): Collection
    {
        return collect($holidays)->map(fn(Holiday $holiday): array => [
            "name" => $holiday->getName([static::LANG_KEY]),
            "date" => Carbon::createFromTimestamp($holiday->getTimestamp(), config("app.timezone")),
        ])->values();
    }
}
