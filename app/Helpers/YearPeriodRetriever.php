<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Illuminate\Contracts\Session\Session;
use Toby\Models\YearPeriod;

class YearPeriodRetriever
{
    public const string SESSION_KEY = "selected_year_period";

    public function __construct(
        protected Session $session,
    ) {}

    public function selected(): YearPeriod
    {
        /** @var YearPeriod $yearPeriod */
        $yearPeriod = YearPeriod::query()->find($this->session->get(static::SESSION_KEY));

        return $yearPeriod !== null ? $yearPeriod : $this->current();
    }

    public function current(): YearPeriod
    {
        return YearPeriod::current();
    }

    public function links(): array
    {
        $selected = $this->selected();
        $current = $this->current();

        $years = YearPeriod::query()->cache()->get();

        $navigation = $years->map(fn(YearPeriod $yearPeriod): array => $this->toNavigation($yearPeriod));

        return [
            "current" => $this->toNavigation($current),
            "selected" => $this->toNavigation($selected),
            "navigation" => $navigation->toArray(),
        ];
    }

    protected function toNavigation(YearPeriod $yearPeriod): array
    {
        return [
            "year" => $yearPeriod->year,
            "link" => route("year-periods.select", $yearPeriod->id),
        ];
    }
}
