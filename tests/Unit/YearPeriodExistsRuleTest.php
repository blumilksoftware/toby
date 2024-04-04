<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Http\Rules\YearPeriodExists;

class YearPeriodExistsRuleTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    protected YearPeriodExists $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = $this->app->make(YearPeriodExists::class);
    }

    public function testItPassesIfYearPeriodExists(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $this->createCurrentYearPeriod();

        $fail = false;
        $value = $now->year;

        $this->rule->validate("year", $value, function () use (&$fail): void {
            $fail = true;
        });
        $this->assertFalse($fail, "The year period for given year does not exist.");
    }

    public function testItFailsIfYearPeriodDoesNotExist(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $invalidValues = [
            $now->year,
            null,
        ];

        foreach ($invalidValues as $value) {
            $fail = false;
            $this->rule->validate("year", $value, static function () use (&$fail): void {
                $fail = true;
            });
            $this->assertTrue($fail, "The year period for given year does not exist.");
        }
    }
}
