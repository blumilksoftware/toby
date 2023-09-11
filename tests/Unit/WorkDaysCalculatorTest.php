<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\WorkDaysCalculator;

class WorkDaysCalculatorTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithSession;
    use InteractsWithYearPeriods;

    public WorkDaysCalculator $workDaysCalculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDaysCalculator = $this->app->make(WorkDaysCalculator::class);

        $yearPeriod = $this->createYearPeriod(2023);

        $polishHolidaysRetriever = $this->app->make(PolishHolidaysRetriever::class);

        foreach ($polishHolidaysRetriever->getForYearPeriod($yearPeriod) as $holiday) {
            $yearPeriod->holidays()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }
    }

    public function testCalculatesCorrectWorkDays(): void
    {
        $actualWorkDays = $this->workDaysCalculator->calculateDays(
            Carbon::parse("2023-01-01"),
            Carbon::parse("2023-12-31"),
        );

        $this->assertEquals(251, $actualWorkDays->count());
    }

    public function testCalculatesCorrectWorkDaysForDelegation(): void
    {
        $actualWorkDays = $this->workDaysCalculator->calculateDays(
            Carbon::parse("2023-01-01"),
            Carbon::parse("2023-12-31"),
            VacationType::Delegation,
        );

        $this->assertEquals(365, $actualWorkDays->count());
    }
}
