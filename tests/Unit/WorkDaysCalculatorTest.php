<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Enums\VacationType;
use Toby\Models\Holiday;

class WorkDaysCalculatorTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithSession;

    public WorkDaysCalculator $workDaysCalculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDaysCalculator = $this->app->make(WorkDaysCalculator::class);

        $polishHolidaysRetriever = $this->app->make(PolishHolidaysRetriever::class);

        foreach ($polishHolidaysRetriever->getForYear(2023) as $holiday) {
            Holiday::query()->create([
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
