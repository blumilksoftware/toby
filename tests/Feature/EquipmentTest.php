<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Models\EquipmentItem;
use Toby\Models\EquipmentLabel;
use Toby\Models\User;

class EquipmentTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeEquipmentList(): void
    {
        EquipmentItem::factory()->count(10)->create();
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseCount("equipment_items", 10);

        $this->actingAs($admin)
            ->get("/equipment-items")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Equipment/Index")
                    ->has("equipmentItems.data", 10),
            );
    }

    public function testEmployeeCannotSeeEquipmentList(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get("/equipment-items")
            ->assertRedirect("/equipment-items/me");
    }

    public function testAnyUserWithProperPermissionCanSeeEquipmentList(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo("manageEquipment");

        $this->actingAs($user)
            ->get("/equipment-items")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page->component("Equipment/Index"),
            );
    }

    public function testAdminCanSearchEquipmentList(): void
    {
        $admin = User::factory()->admin()->create();

        EquipmentItem::factory()
            ->count(4)
            ->sequence(
                [
                    "name" => "Test1",
                ],
                [
                    "name" => "Test2",
                ],
                [
                    "name" => "Test3",
                ],
                [
                    "name" => "Item1",
                ],
            )
            ->create();

        $this->assertDatabaseCount("equipment_items", 4);

        $this->actingAs($admin)
            ->get("/equipment-items?search=test")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Equipment/Index")
                    ->has("equipmentItems.data", 3),
            );
    }

    public function testAdminCanFilterEquipmentListWithLabels(): void
    {
        $admin = User::factory()->admin()->create();

        EquipmentItem::factory()
            ->count(4)
            ->sequence(
                [
                    "labels" => [
                        "Test1",
                        "Test2",
                    ],
                ],
                [
                    "labels" => [
                        "Test1",
                    ],
                ],
                [
                    "labels" => [
                        "Test2",
                    ],
                ],
                [
                    "labels" => [
                        "Test3",
                    ],
                ],
            )
            ->create();

        $this->assertDatabaseCount("equipment_items", 4);

        $this->actingAs($admin)
            ->get("/equipment-items?labels[]=Test1&labels[]=Test2")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Equipment/Index")
                    ->has("equipmentItems.data", 3),
            );
    }

    public function testEquipmentListIsPaginated(): void
    {
        EquipmentItem::factory()->count(16)->create();
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseCount("equipment_items", 16);

        $this->actingAs($admin)
            ->get("/equipment-items?page=2")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Equipment/Index")
                    ->has("equipmentItems.data", 1),
            );
    }

    public function testAdminCanCreateEquipmentItem(): void
    {
        $admin = User::factory()->admin()->create();
        $assignee = User::factory()->create();

        $this->actingAs($admin)
            ->post("/equipment-items", [
                "idNumber" => "123",
                "name" => "Test",
                "isMobile" => true,
                "assignee" => $assignee->id,
                "assignedAt" => "2023-01-01",
                "labels" => ["Test1", "Test2"],
            ])
            ->assertSessionHasNoErrors();

        $equipmentItem = EquipmentItem::query()->where("id_number", "123")->first();

        $this->assertDatabaseHas("equipment_items", [
            "id_number" => "123",
            "name" => "Test",
            "is_mobile" => true,
            "assignee_id" => $assignee->id,
            "assigned_at" => "2023-01-01",
        ]);

        $this->assertEquals(["Test1", "Test2"], $equipmentItem->labels->toArray());
    }

    public function testAdminCanEditEquipmentItem(): void
    {
        $admin = User::factory()->admin()->create();

        $equipmentItem = EquipmentItem::factory()->create();

        $this->assertDatabaseHas("equipment_items", [
            "id" => $equipmentItem->id,
            "id_number" => $equipmentItem->id_number,
            "name" => $equipmentItem->name,
            "is_mobile" => $equipmentItem->is_mobile,
            "assignee_id" => $equipmentItem->assignee_id,
            "assigned_at" => $equipmentItem->assigned_at,
        ]);

        $this->actingAs($admin)
            ->put("/equipment-items/{$equipmentItem->id}", [
                "idNumber" => "123",
                "name" => "Test",
                "isMobile" => true,
                "assignee" => null,
                "assignedAt" => null,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("equipment_items", [
            "id" => $equipmentItem->id,
            "id_number" => "123",
            "name" => "Test",
            "is_mobile" => true,
            "assignee_id" => null,
            "assigned_at" => null,
        ]);
    }

    public function testAdminCanDeleteEquipmentItem(): void
    {
        $admin = User::factory()->admin()->create();

        $equipmentItem = EquipmentItem::factory()->create();

        $this->actingAs($admin)
            ->delete("/equipment-items/{$equipmentItem->id}")
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($equipmentItem);
    }

    public function testAdminCanSeeEquipmentLabelList(): void
    {
        EquipmentLabel::factory()->count(10)->create();
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseCount("equipment_labels", 10);

        $this->actingAs($admin)
            ->get("/equipment-labels")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("EquipmentLabels")
                    ->has("labels.data", 10),
            );
    }

    public function testAdminCanCreateEquipmentLabel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post("/equipment-labels", [
                "name" => "Test",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("equipment_labels", [
            "name" => "Test",
        ]);
    }

    public function testAdminCanDeleteEquipmentLabel(): void
    {
        $admin = User::factory()->admin()->create();

        $equipmentLabel = EquipmentLabel::factory()->create();

        $this->actingAs($admin)
            ->delete("/equipment-labels/{$equipmentLabel->id}")
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($equipmentLabel);
    }

    public function testEmployeeCanOwnEquipment(): void
    {
        $employee = User::factory()->employee()->create();
        EquipmentItem::factory()
            ->count(10)
            ->for($employee, "assignee")
            ->create();

        $this->actingAs($employee)
            ->get("/equipment-items/me")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Equipment/IndexForEmployee")
                    ->has("equipmentItems.data", 10),
            );
    }
}
