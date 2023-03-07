<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ResumePage;
use Tests\DuskTestCase;
use Toby\Eloquent\Models\User;

class CreateResumeTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed("DuskSeeder");

        $this->user = User::all()->last();
    }

    public function testAdminCanCreateResume(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new ResumePage())
                ->waitFor("@create-resume-button")
                ->click("@create-resume-button")
                ->waitFor("@users-listbox-button")
                ->click("@users-listbox-button")
                ->click("@non-existing-user")
                ->type("#name", "Nataniel Wysocki")
                ->click("@add-school")
                ->dropDownButton()
                ->waitFor("@school-name")
                ->type("@school-name", "Technikum nr 5 w Legnicy")
                ->type("@school-degree", "Średnie")
                ->type("@school-fieldofstudy", "Informatyka")
                ->click("@school-start-date")
                ->fillYear(2016)
                ->changeMonthInResume(9)
                ->click("@school-end-date")
                ->fillYear(2019)
                ->changeMonthInResume(5)
                ->click("@add-language")
                ->dropDownButton()
                ->enterValue("language", 0, "Polish")
                ->click("@add-technologies")
                ->dropDownButton()
                ->enterValue("technology", 0, "Dusk")
                ->click("@add-project")
                ->dropDownButton()
                ->type("@project-text", "Aplikacja do testowania")
                ->enterProjectTechnology(0, "Dusk")
                ->click("@project-text")
                ->enterProjectTechnology(0, "Cypress")
                ->click("@project-text")
                ->click("@project-start-date")
                ->fillYear(2022)
                ->changeMonthInResume(10)
                ->click("@project-in-work-date")
                ->type("@project-tasks", "Robienie testów")
                ->click("@save-resume-button")
                ->waitForTextIn("@resume-name", "Nataniel Wysocki");
        });
    }
}
