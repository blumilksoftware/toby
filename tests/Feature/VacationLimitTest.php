<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\YearPeriod;

class VacationLimitTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeVacationLimits(): void
    {
        $admin = User::factory()->admin()->create();

        VacationLimit::factory(10)
            ->for(YearPeriod::current())
            ->create();

        $this->actingAs($admin)
            ->get("/vacation/limits")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("VacationLimits")
                    ->has("limits", 10),
            );
    }

    public function testAdminCanUpdateVacationLimits(): void
    {
        $admin = User::factory()->admin()->create();

        VacationLimit::factory(3)
            ->for(YearPeriod::current())
            ->create();

        [$limit1, $limit2, $limit3] = VacationLimit::all();

        $data = [
            [
                "id" => $limit1->id,
                "days" => 25,
            ],
            [
                "id" => $limit2->id,
                "days" => null,
            ],
            [
                "id" => $limit3->id,
                "days" => 20,
            ],
        ];

        $this->actingAs($admin)
            ->put("/vacation/limits", [
                "items" => $data,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_limits", [
            "id" => $limit1->id,
            "days" => 25,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "id" => $limit2->id,
            "days" => null,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "id" => $limit3->id,
            "days" => 20,
        ]);
    }
}
