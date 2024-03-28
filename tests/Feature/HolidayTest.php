<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Models\Holiday;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class HolidayTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeHolidayList(): void
    {
        Holiday::factory()->count(10)->create();
        $user = User::factory()->create();

        $this->assertDatabaseCount("holidays", 10);

        $this->actingAs($user)
            ->get("/holidays")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Holidays/Index")
                    ->has("holidays.data", 10),
            );
    }

    public function testAdminCanCreateHoliday(): void
    {
        $admin = User::factory()->admin()->create();

        $currentYearPeriod = YearPeriod::current();

        $this->actingAs($admin)
            ->post("/holidays", [
                "name" => "Holiday 1",
                "date" => Carbon::create($currentYearPeriod->year, 5, 20)->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("holidays", [
            "name" => "Holiday 1",
            "date" => Carbon::create($currentYearPeriod->year, 5, 20),
            "year_period_id" => YearPeriod::current()->id,
        ]);
    }

    public function testAdminCannotCreateHolidayForYearPeriodThatDoesntExist(): void
    {
        $admin = User::factory()->admin()->create();

        $year = YearPeriod::query()->max("year") + 1;

        $this->actingAs($admin)
            ->post("/holidays", [
                "name" => "Holiday 1",
                "date" => Carbon::create($year, 5, 20)->toDateString(),
            ])
            ->assertSessionHasErrors(["date"]);
    }

    public function testAdminCannotCreateHolidayIfGivenDataIsUsed(): void
    {
        $admin = User::factory()->admin()->create();

        $currentYearPeriod = YearPeriod::current();
        $sameDate = Carbon::create($currentYearPeriod->year, 5, 20)->toDateString();

        Holiday::factory()->create([
            "name" => "Holiday",
            "date" => $sameDate,
        ]);

        $this->actingAs($admin)
            ->post("/holidays", [
                "name" => "Holiday 1",
                "date" => $sameDate,
            ])
            ->assertSessionHasErrors(["date"]);
    }

    public function testAdminCanEditHoliday(): void
    {
        $admin = User::factory()->admin()->create();

        $currentYearPeriod = YearPeriod::current();

        $holiday = Holiday::factory()->create([
            "name" => "Name to change",
            "date" => Carbon::create($currentYearPeriod->year, 5, 20),
        ]);

        $this->assertDatabaseHas("holidays", [
            "name" => $holiday->name,
            "date" => $holiday->date->toDateString(),
            "year_period_id" => $currentYearPeriod->id,
        ]);

        $this->actingAs($admin)
            ->put("/holidays/{$holiday->id}", [
                "name" => "Holiday 1",
                "date" => Carbon::create($currentYearPeriod->year, 10, 25)->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("holidays", [
            "name" => "Holiday 1",
            "date" => Carbon::create($currentYearPeriod->year, 10, 25)->toDateString(),
            "year_period_id" => $currentYearPeriod->id,
        ]);
    }

    public function testAdminCanDeleteHoliday(): void
    {
        $admin = User::factory()->admin()->create();

        $holiday = Holiday::factory()->create();

        $this->actingAs($admin)
            ->delete("/holidays/{$holiday->id}")
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($holiday);
    }
}
