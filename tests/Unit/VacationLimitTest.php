<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Actions\CreateUserAction;
use Toby\Domain\Actions\CreateYearPeriodAction;
use Toby\Models\Profile;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class VacationLimitTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCurrentYearPeriod();
    }

    public function testWhenUserIsCreatedThenVacationLimitIsCreatedForCurrentYearPeriod(): void
    {
        $this->assertDatabaseCount("vacation_limits", 0);

        $currentYearPeriod = YearPeriod::current();
        $createUserAction = $this->app->make(CreateUserAction::class);

        $dumpUserData = User::factory()->raw();
        $dumpProfileData = Profile::factory()->raw();

        $user = $createUserAction->execute($dumpUserData, $dumpProfileData);

        $this->assertDatabaseCount("vacation_limits", 1);

        $this->assertDatabaseHas("vacation_limits", [
            "user_id" => $user->id,
            "year_period_id" => $currentYearPeriod->id,
        ]);
    }

    public function testWhenYearPeriodIsCreatedThenVacationLimitsForThisYearPeriodAreCreated(): void
    {
        $this->assertDatabaseCount("vacation_limits", 0);
        $createYearPeriodAction = $this->app->make(CreateYearPeriodAction::class);
        $lastYear = YearPeriod::query()->max("year") + 1;

        User::factory(10)->create();

        $createYearPeriodAction->execute($lastYear);

        $this->assertDatabaseCount("vacation_limits", 10);
    }
}
