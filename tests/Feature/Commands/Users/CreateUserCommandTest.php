<?php

declare(strict_types=1);

namespace Tests\Feature\Commands\Users;

use Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\FeatureTestCase;
use Toby\Console\Commands\Users\CreateUser;
use Toby\Models\User;

class CreateUserCommandTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public static function userRolesProvider(): Generator
    {
        yield "administrator role" => [
            "role" => "administrator",
        ];

        yield "employee role" => [
            "role" => "employee",
        ];

        yield "technical_approver role" => [
            "role" => "technical_approver",
        ];

        yield "administrative_approver role" => [
            "role" => "administrative_approver",
        ];
    }

    public function testUserCanBeCreated(): void
    {
        $this->artisan(CreateUser::class, ["email" => "test@example.com"])
            ->expectsOutput("User has been created.");

        $this->assertDatabaseHas(
            table: "users",
            data: [
                "email" => "test@example.com",
            ],
        );
    }

    #[DataProvider("userRolesProvider")]
    public function testUserCanBeCreatedWithExpectedRole(string $role): void
    {
        $this->artisan(CreateUser::class, ["email" => "test@example.com", "--role" => $role])
            ->expectsOutput("User has been created.");

        $this->assertDatabaseHas(
            table: "users",
            data: [
                "email" => "test@example.com",
                "role" => $role,
            ],
        );
    }

    public function testUserWithTheSameEmailCannotBeCreated(): void
    {
        User::factory(
            ["email" => "test@example.com"],
        )->create();

        $this->artisan(CreateUser::class, ["email" => "test@example.com"])
            ->expectsOutput("Email already exists.");

        $this->assertDatabaseCount(table: "users", count: 1);
    }

    public function testUserCannotBeCreatedInProductionEnvironment(): void
    {
        app()->detectEnvironment(fn(): string => "production");

        $this->assertTrue(app()->isProduction(), message: "Application is not in the production mode.");

        $this->artisan(CreateUser::class, ["email" => "test@example.com"])
            ->expectsOutput("User cannot be created in production environment.");

        app()->detectEnvironment(fn(): string => "testing");

        $this->assertDatabaseCount(table: "users", count: 0);
    }

    public function testUserWithInvalidRoleCannotBeCreated(): void
    {
        $this->artisan(CreateUser::class, ["email" => "test@example.com", "--role" => "invalid"])
            ->expectsOutput("Invalid role.");

        $this->assertDatabaseCount(table: "users", count: 0);
    }
}
