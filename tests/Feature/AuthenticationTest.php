<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FeatureTestCase;
use Toby\Models\User;

class AuthenticationTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testGuestIsRedirectedFromDashboard(): void
    {
        $this->get("/")
            ->assertRedirect();
    }

    public function testUserIsNotRedirectedFromDashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/")
            ->assertOk();
    }

    public function testUserCanLogout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertAuthenticated();

        $this->post("/logout")
            ->assertRedirect();

        $this->assertGuest();
    }
}
