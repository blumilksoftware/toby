<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Models\User;
use Toby\Models\VacationLimit;

class VacationLimitTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeVacationLimits(): void
    {
        $admin = User::factory()->createQuietly();

        User::factory(10)->create();

        $this->actingAs($admin)
            ->get("/vacation-limits")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("VacationLimits")
                    ->has("limits.data", 10)
            );
    }

    public function testAdminCanUpdateVacationLimits(): void
    {
        $admin = User::factory()->createQuietly();

        User::factory(3)->create();

        [$limit1, $limit2, $limit3] = VacationLimit::all();

        $data = [
            [
                "id" => $limit1->id,
                "hasVacation" => true,
                "days" => 25,
            ],
            [
                "id" => $limit2->id,
                "hasVacation" => false,
                "days" => null,
            ],
            [
                "id" => $limit3->id,
                "hasVacation" => true,
                "days" => 20,
            ],
        ];

        $this->actingAs($admin)
            ->put("/vacation-limits", [
                "items" => $data
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_limits", [
           "id" => $limit1->id,
           "has_vacation" => true,
           "days" => 25,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "id" => $limit2->id,
            "has_vacation" => false,
            "days" => null,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "id" => $limit3->id,
            "has_vacation" => true,
            "days" => 20,
        ]);
    }
}
