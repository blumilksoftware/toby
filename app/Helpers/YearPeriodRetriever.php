<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Toby\Models\YearPeriod;

class YearPeriodRetriever
{
    public const SESSION_KEY = "selected_year_period";

    public function selected(): YearPeriod
    {
        /** @var YearPeriod $yearPeriod */
        $yearPeriod = YearPeriod::query()->find(session()->get(static::SESSION_KEY));

        return $yearPeriod !== null ? $yearPeriod : $this->current();
    }

    public function current(): YearPeriod
    {
        return YearPeriod::current();
    }

    public function links(): array
    {
        $current = $this->selected();

        $years = YearPeriod::query()->whereIn("year", $this->offset($current->year))->get();
        $navigation = $years->map(fn(YearPeriod $yearPeriod) => $this->toNavigation($yearPeriod));

        return [
            "current" => $current->year,
            "navigation" => $navigation->toArray(),
        ];
    }

    protected function offset(int $year): array
    {
        return range($year - 2, $year + 2);
    }

    protected function toNavigation(YearPeriod $yearPeriod): array
    {
        return [
            "year" => $yearPeriod->year,
            "link" => route("year-periods.select", $yearPeriod->id),
        ];
    }
}
