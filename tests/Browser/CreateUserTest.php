<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\UsersPage;
use Tests\DuskTestCase;
use Toby\Eloquent\Models\User;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::all()->last();
    }

    public function testAdminCanCreateUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new UsersPage())
                ->waitFor("@create-user-button")
                ->click("@create-user-button")
                ->waitFor("#firstName")
                ->type("#firstName", "Nataniel")
                ->type("#lastName", "Wysocki")
                ->type("#email", "NatanielW@example.com")
                ->type("#position", "Programista")
                ->click("@employment-date")
                ->fillMonth(1)
                ->fillDay(7)
                ->type("#slack", "1920")
                ->click("@birthday")
                ->fillYear(1999)
                ->fillMonth(12)
                ->fillDay(27)
                ->click("@save-user-button")
                ->waitForText("Nataniel Wysocki");
        });
    }
}
