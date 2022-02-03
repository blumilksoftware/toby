<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class UserTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeUsersList(): void
    {
        User::factory()->count(10)->create();
        $admin = User::factory()->create();

        $this->assertDatabaseCount("users", 11);

        $this->actingAs($admin)
            ->get("/users")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Users/Index")
                    ->has("users.data", 11),
            );
    }

    public function testAdminCanSearchUsersList(): void
    {
        User::factory([
            "first_name" => "Test",
            "last_name" => "User1",
        ])->create();
        User::factory([
            "first_name" => "Test",
            "last_name" => "User2",
        ])->create();
        User::factory([
            "first_name" => "Test",
            "last_name" => "User3",
        ])->create();
        $admin = User::factory([
            "first_name" => "John",
            "last_name" => "Doe",
        ])->create();

        $this->assertDatabaseCount("users", 4);

        $this->actingAs($admin)
            ->get("/users?search=test")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Users/Index")
                    ->has("users.data", 3),
            );
    }

    public function testUserListIsPaginated(): void
    {
        User::factory()->count(15)->create();
        $admin = User::factory()->create();

        $this->assertDatabaseCount("users", 16);

        $this->actingAs($admin)
            ->get("/users?page=2")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Users/Index")
                    ->has("users.data", 1),
            );
    }

    public function testAdminCanCreateUser(): void
    {
        $admin = User::factory()->create();
        Carbon::setTestNow(Carbon::now());

        $this->actingAs($admin)
            ->post("/users", [
                "firstName" => "John",
                "lastName" => "Doe",
                "role" => Role::EMPLOYEE->value,
                "email" => "john.doe@example.com",
                "employmentForm" => EmploymentForm::B2B_CONTRACT->value,
                "employmentDate" => Carbon::now()->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("users", [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "employment_form" => EmploymentForm::B2B_CONTRACT->value,
            "employment_date" => Carbon::now()->toDateString(),
        ]);
    }

    public function testAdminCanEditUser(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        Carbon::setTestNow();

        $this->assertDatabaseHas("users", [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "employment_form" => $user->employment_form->value,
            "employment_date" => $user->employment_date->toDateString(),
        ]);

        $this->actingAs($admin)
            ->put("/users/{$user->id}", [
                "firstName" => "John",
                "lastName" => "Doe",
                "email" => "john.doe@example.com",
                "role" => Role::EMPLOYEE->value,
                "employmentForm" => EmploymentForm::B2B_CONTRACT->value,
                "employmentDate" => Carbon::now()->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("users", [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "employment_form" => EmploymentForm::B2B_CONTRACT->value,
            "employment_date" => Carbon::now()->toDateString(),
        ]);
    }

    public function testAdminCanDeleteUser(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/users/{$user->id}")
            ->assertSessionHasNoErrors();

        $this->assertSoftDeleted($user);
    }

    public function testAdminCanRestoreUser(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted($user);

        $this->actingAs($admin)
            ->post("/users/{$user->id}/restore")
            ->assertSessionHasNoErrors();

        $this->assertNotSoftDeleted($user);
    }
}
