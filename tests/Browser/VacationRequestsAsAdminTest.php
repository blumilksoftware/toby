<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\RequestPage;
use Tests\DuskTestCase;
use Toby\Eloquent\Models\User;

class VacationRequestsAsAdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed("DuskSeeder");

        $this->user = User::all()->last();
    }

    public function testAdminCanCreateVacationRequest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new RequestPage())
                ->waitFor("@create-vacation-request-button")
                ->click("@create-vacation-request-button")
                ->waitFor("@vacation-types-listbox-button")
                ->waitFor("@date-to")
                ->waitFor("@date-from")
                ->click("@date-from")
                ->fillMonth(11)
                ->fillDay(8)
                ->click("@date-to")
                ->fillMonth(11)
                ->fillDay(9)
                ->type("#comment", "Zwolnienie.")
                ->click("@flowSkipped")
                ->click("@save-request-button")
                ->waitForText("Zatwierdzony");
        });
    }
}
