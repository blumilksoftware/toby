<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\User;

class AssignedBenefitsTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeAssignedBenefits(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory(4)->create();
        Benefit::factory(4)->create();

        /** @var BenefitsReport $assignedBenefits */
        $assignedBenefits = BenefitsReport::query()
            ->whereKey(1)
            ->first();

        $assignedBenefits->refresh();

        $this->actingAs($admin)
            ->get("/assigned-benefits")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("BenefitsReport/AssignedBenefits")
                    ->where("assignedBenefits.name", $assignedBenefits->name)
                    ->where("assignedBenefits.data", $assignedBenefits->data),
            );

        $this->assertDatabaseHas("reports", [
            "id" => $assignedBenefits->id,
            "name" => $assignedBenefits->name,
        ]);
    }

    public function testAdminCanEditAssignedBenefits(): void
    {
        $admin = User::factory()->admin()->create();

        [$firstBenefit, $secondBenefit] = Benefit::factory(2)->create();
        [$firstUser, $secondUser] = User::factory(2)->create();

        /** @var BenefitsReport $assignedBenefits */
        $assignedBenefits = BenefitsReport::query()
            ->whereKey(1)
            ->first();

        $assignedBenefits->refresh();

        $data = [
            [
                "user" => $firstUser->id,
                "comment" => "Test Comment for user {$firstUser->profile->fullname}",
                "benefits" => [
                    [
                        "id" => $firstBenefit->id,
                        "employee" => null,
                        "employer" => null,
                    ],
                    [
                        "id" => $secondBenefit->id,
                        "employee" => 10000,
                        "employer" => 100,
                    ],
                ],
            ],
            [
                "user" => $secondUser->id,
                "comment" => "Test Comment for user {$secondUser->profile->fullname}",
                "benefits" => [
                    [
                        "id" => $firstBenefit->id,
                        "employee" => 20000,
                        "employer" => 200,
                    ],
                    [
                        "id" => $secondBenefit->id,
                        "employee" => null,
                        "employer" => null,
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)
            ->put("/assigned-benefits", [
                "name" => "current",
                "users" => null,
                "benefits" => null,
                "data" => $data,
                "committed_at" => null,
            ])
            ->assertSessionHasNoErrors();

        $assignedBenefits->refresh();

        $this->assertDatabaseHas("reports", [
            "id" => $assignedBenefits->id,
            "name" => $assignedBenefits->name,
            "users" => null,
            "benefits" => null,
            "committed_at" => null,
        ]);

        $this->assertEquals($data, $assignedBenefits->data);
    }
}
