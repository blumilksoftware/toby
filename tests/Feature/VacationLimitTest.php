<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Models\User;
use Toby\Models\VacationLimit;

class VacationLimitTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeVacationLimits(): void
    {
        $admin = User::factory()->admin()->create();

        VacationLimit::factory(10)
            ->create(["year" => Carbon::now()->year]);

        $this->actingAs($admin)
            ->get("/vacation/limits")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("VacationLimits")
                    ->has("limits", 11),
            );
    }

    public function testAdminCanUpdateVacationLimits(): void
    {
        $admin = User::factory()->admin()->create();

        $year = Carbon::now()->year;

        [$user1, $user2, $user3] = User::factory(3)->create();

        $data = [
            [
                "user" => $user1->id,
                "year" => $year,
                "days" => 25,
            ],
            [
                "user" => $user2->id,
                "year" => $year,
                "days" => null,
            ],
            [
                "user" => $user3->id,
                "year" => $year,
                "days" => 20,
            ],
        ];

        $this->actingAs($admin)
            ->put("/vacation/limits", [
                "items" => $data,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_limits", [
            "user_id" => $user1->id,
            "year" => $year,
            "days" => 25,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "user_id" => $user2->id,
            "year" => $year,
            "days" => null,
        ]);

        $this->assertDatabaseHas("vacation_limits", [
            "user_id" => $user3->id,
            "year" => $year,
            "days" => 20,
        ]);
    }
}
