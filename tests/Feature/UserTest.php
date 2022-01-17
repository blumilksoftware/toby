<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Toby\Models\User;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeUsersList(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/users")
            ->assertOk();
    }
}
