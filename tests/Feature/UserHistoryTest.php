<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Enums\EmploymentForm;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;

class UserHistoryTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()
            ->admin()
            ->create();
        $this->user = User::factory()
            ->create();
        $this->userHistory = $this->user->histories()->create([
            "from" => Carbon::now()->subDays(10),
            "to" => Carbon::now()->subDays(5),
            "type" => UserHistoryType::Employment,
            "employment_form" => EmploymentForm::EmploymentContract,
        ]);
    }

    public function testAdminCanSeeUserHistoryList(): void
    {
        $this->actingAs($this->admin)
            ->get("/users/{$this->user->id}/history")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("UserHistory/Index")
                    ->has("history.data", 1),
            );
    }

    public function testAdminCanCreateUserHistory(): void
    {
        $this->actingAs($this->admin)
            ->post("/users/{$this->user->id}/history", [
                "from" => Carbon::now()->subDays(100)->format("Y-m-d"),
                "to" => Carbon::now()->subDays(50)->format("Y-m-d"),
                "type" => UserHistoryType::Employment->value,
                "employmentForm" => EmploymentForm::EmploymentContract->value,
            ])
            ->assertRedirect("/users/{$this->user->id}/history");

        $this->assertDatabaseHas("user_histories", [
            "user_id" => $this->user->id,
            "from" => Carbon::now()->subDays(100)->format("Y-m-d"),
            "to" => Carbon::now()->subDays(50)->format("Y-m-d"),
            "type" => UserHistoryType::Employment->value,
            "employment_form" => EmploymentForm::EmploymentContract->value,
        ]);
    }

    public function testAdminCanEditUserHistory(): void
    {
        $this->actingAs($this->admin)
            ->get("/users/history/{$this->userHistory->id}")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("UserHistory/Edit")
                    ->has("history"),
            );
    }

    public function testAdminCanUpdateUserHistory(): void
    {
        $this->actingAs($this->admin)
            ->put("/users/history/{$this->userHistory->id}", [
                "from" => Carbon::now()->subDays(200)->format("Y-m-d"),
                "to" => Carbon::now()->subDays(70)->format("Y-m-d"),
                "type" => UserHistoryType::MedicalExam->value,
            ])
            ->assertRedirect("/users/{$this->user->id}/history");

        $this->assertDatabaseHas("user_histories", [
            "user_id" => $this->user->id,
            "from" => Carbon::now()->subDays(200)->format("Y-m-d"),
            "to" => Carbon::now()->subDays(70)->format("Y-m-d"),
            "type" => UserHistoryType::MedicalExam->value,
        ]);
    }

    public function testAdminCanDeleteUserHistory(): void
    {
        $this->actingAs($this->admin)
            ->delete("/users/history/{$this->userHistory->id}");

        $this->assertDatabaseMissing("user_histories", [
            "id" => $this->userHistory->id,
        ]);
    }
}
