<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Infrastructure\Jobs\CheckYearPeriod;

class CheckYearPeriodTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    public function testYearPeriodsAreCreatedWhenDontExist(): void
    {
        $now = Carbon::now();

        Carbon::setTestNow($now);

        CheckYearPeriod::dispatchSync();

        $this->assertDatabaseCount("year_periods", 2);

        $this->assertDatabaseHas("year_periods", [
            "year" => $now->year,
        ]);

        $this->assertDatabaseHas("year_periods", [
            "year" => $now->year + 1,
        ]);
    }

    public function testCurrentYearPeriodIsCreatedWhenDoesntExist(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->assertDatabaseMissing("year_periods", [
            "year" => $now->year,
        ]);

        CheckYearPeriod::dispatchSync();

        $this->assertDatabaseHas("year_periods", [
            "year" => $now->year,
        ]);
    }

    public function testNextYearPeriodIsCreatedWhenDoesntExist(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->createCurrentYearPeriod();

        $this->assertDatabaseMissing("year_periods", [
            "year" => $now->year + 1,
        ]);

        CheckYearPeriod::dispatchSync();

        $this->assertDatabaseHas("year_periods", [
            "year" => $now->year + 1,
        ]);
    }

    public function testYearPeriodsAreNotCreatedWhenExists(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->createCurrentYearPeriod();
        $this->createYearPeriod($now->year + 1);

        $this->assertDatabaseCount("year_periods", 2);

        CheckYearPeriod::dispatchSync();

        $this->assertDatabaseCount("year_periods", 2);
    }

    public function testYearPeriodsAreNotDuplicatedWhenJobCalledMultipleTimes(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        CheckYearPeriod::dispatchSync();
        $this->assertDatabaseCount("year_periods", 2);

        CheckYearPeriod::dispatchSync();
        CheckYearPeriod::dispatchSync();
        CheckYearPeriod::dispatchSync();

        $this->assertDatabaseCount("year_periods", 2);
    }
}
