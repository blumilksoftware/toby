<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Toby\Models\User;

class AvatarTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();
    }

    public function testAvatarIsGeneratedWhenUserIsCreated(): void
    {
        $user = User::factory()->create();

        Storage::assertExists($user->avatar);
    }

    public function testAvatarIsDeletedWhenUserIsForceDeleted(): void
    {
        $user = User::factory()->create();

        Storage::assertExists($user->avatar);

        $user->forceDelete();

        Storage::assertMissing($user->avatar);

    }

    public function testAvatarIsReplacedWhenUserChangedTheirName(): void
    {
        $user = User::factory()->create();
        $oldAvatar = $user->avatar;

        Storage::assertExists($oldAvatar);

        $user->update(["name" => "John Doe"]);

        Storage::assertMissing($oldAvatar);
        Storage::assertExists($user->avatar);
    }

    public function testAvatarIsNotReplacedWhenUserChangedOtherData(): void
    {
        $user = User::factory()->create();
        $avatar = $user->avatar;

        Storage::assertExists($avatar);

        $user->update(["email" => "john.doe@example.com"]);

        Storage::assertExists($avatar);
    }
}