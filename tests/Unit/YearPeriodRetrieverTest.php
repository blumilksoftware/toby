<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\YearPeriod;

class YearPeriodRetrieverTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithSession;
    use InteractsWithYearPeriods;

    public Carbon $current;
    public YearPeriod $previousYearPeriod;
    public YearPeriod $currentYearPeriod;
    public YearPeriod $nextYearPeriod;
    public YearPeriodRetriever $yearPeriodRetriever;

    public function setUp(): void
    {
        parent::setUp();

        $this->current = Carbon::now();
        Carbon::setTestNow($this->current);

        $this->yearPeriodRetriever = new YearPeriodRetriever();

        $this->previousYearPeriod = $this->createYearPeriod($this->current->year - 1);
        $this->currentYearPeriod = $this->createCurrentYearPeriod();
        $this->nextYearPeriod = $this->createYearPeriod($this->current->year + 1);
    }

    public function testRetrievesCorrectCurrentYearPeriod(): void
    {
        $this->assertSame($this->currentYearPeriod->id, $this->yearPeriodRetriever->current()->id);
    }

    public function testRetrievesCurrentYearPeriodWhenNoSelected(): void
    {
        $this->clearSelectedYearPeriod();

        $this->assertSame($this->currentYearPeriod->id, $this->yearPeriodRetriever->selected()->id);
    }

    public function testRetrievesCorrectYearPeriodWhenSelected(): void
    {
        $this->markYearPeriodAsSelected($this->nextYearPeriod);

        $this->assertSame($this->nextYearPeriod->id, $this->yearPeriodRetriever->selected()->id);
    }

    public function testLinks(): void
    {
        $expected = [
            "current" => $this->current->year,
            "navigation" => [
                [
                    "year" => $this->previousYearPeriod->year,
                    "link" => route("year-periods.select", $this->previousYearPeriod)
                ],
                [
                    "year" => $this->currentYearPeriod->year,
                    "link" => route("year-periods.select", $this->currentYearPeriod)
                ],
                [
                    "year" => $this->nextYearPeriod->year,
                    "link" => route("year-periods.select", $this->nextYearPeriod)
                ],
            ]
        ];


        $this->assertSame($expected, $this->yearPeriodRetriever->links());
    }
}
