<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

class KeysTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeKeyList(): void
    {
        Key::factory()->count(10)->create();
        $user = User::factory()->create();

        $this->assertDatabaseCount("keys", 10);

        $this->actingAs($user)
            ->get("/keys")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Keys")
                    ->has("keys.data", 10),
            );
    }

    public function testAdminCanCreateKey(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseMissing("keys", [
            "user_id" => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post("/keys")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("keys", [
            "user_id" => $admin->id,
        ]);
    }

    public function testUserCannotCreateKey(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post("/keys")
            ->assertForbidden();
    }

    public function testAdminCanDeleteKey(): void
    {
        $admin = User::factory()->admin()->create();

        $key = Key::factory()->create();

        $this->actingAs($admin)
            ->delete("/keys/{$key->id}")
            ->assertRedirect();
    }

    public function testUserCanTakeKeyFromAnotherUser(): void
    {
        $user = User::factory()->create();
        $userWithKey = User::factory()->create();

        $key = Key::factory()->for($userWithKey)->create();

        $this->assertDatabaseHas("keys", [
            "id" => $key->id,
            "user_id" => $userWithKey->id,
        ]);

        $this->actingAs($user)
            ->post("/keys/{$key->id}/take")
            ->assertRedirect();

        $this->assertDatabaseHas("keys", [
            "id" => $key->id,
            "user_id" => $user->id,
        ]);
    }

    public function testUserCanGiveTheirKeyToAnotherUser(): void
    {
        $userWithKey = User::factory()->create();
        $user = User::factory()->create();

        $key = Key::factory()->for($userWithKey)->create();

        $this->assertDatabaseHas("keys", [
            "id" => $key->id,
            "user_id" => $userWithKey->id,
        ]);

        $this->actingAs($userWithKey)
            ->post("/keys/{$key->id}/give", [
                "user" => $user->id,
            ])
            ->assertSessionhasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("keys", [
            "id" => $key->id,
            "user_id" => $user->id,
        ]);
    }
}
