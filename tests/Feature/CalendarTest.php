<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FeatureTestCase;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class CalendarTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanChangeYearPeriodOnTheCalendar(): void
    {
        $currentYearPeriod = YearPeriod::current();
        $nextYearPeriod = YearPeriod::query()
            ->create([
                "year" => $currentYearPeriod->year + 1,
            ]);

        $this->assertNotEquals($currentYearPeriod->year, $nextYearPeriod->year);

        $this->actingAs($this->user)
            ->get("/calendar/january/$nextYearPeriod->year")
            ->assertRedirect();

        $selectedYearPeriod = YearPeriod::query()->where("id", session()->get("selected_year_period"))->first();
        $this->assertEquals($selectedYearPeriod->year, $nextYearPeriod->year);
    }

    public function testUserCannotChangeYearPeriodIfYearPeriodDoesNotExist(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $this->actingAs($this->user)
            ->get("/calendar/january/" . ($currentYearPeriod->year + 1))
            ->assertStatus(404);

        $this->actingAs($this->user)
            ->get("/calendar/january/$currentYearPeriod->year")
            ->assertStatus(302);
    }

    public function testUserCanSeeCalendar(): void
    {
        $this->actingAs($this->user)
            ->get("/calendar")
            ->assertOk();
    }
}
