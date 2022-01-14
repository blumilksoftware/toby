<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Toby\Models\User;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testUserCanLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit("/")
                ->assertVisible("@user-menu")
                ->click("@user-menu")
                ->assertVisible("@user-menu-list")
                ->assertSee("Sign out")
                ->press("Sign out")
                ->assertPathIs("/");
        });
    }
}
