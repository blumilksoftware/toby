<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\MyRequestPage;
use Tests\Browser\Pages\RequestPage;
use Tests\DuskTestCase;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class VacationRequestsAsEmployeeTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed("DuskSeeder");
        $this->admin = User::all()->last();
        $this->employee = User::where("email", "anna.nowak@example.com")->first();

        $this->technical = User::factory([
            "email" => "maciej.ziolkowski@example.com",
            "role" => Role::TechnicalApprover,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Maciej",
                "last_name" => "Ziółkowski",
                "employment_form" => EmploymentForm::BoardMemberContract,
                "position" => "programista",
                "employment_date" => Carbon::createFromDate(2021, 1, 4),
            ])
            ->create();
    }

    public function testUserCanCreateVacationRequest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->employee)
                ->visit(new MyRequestPage())
                ->waitFor("@create-vacation-request-button")
                ->click("@create-vacation-request-button")
                ->waitFor("@vacation-types-listbox-button")
                ->click("@vacation-types-listbox-button")
                ->waitUntilMissingText("Ładowanie...")
                ->click("@unpaid_vacation")
                ->waitFor("@date-from")
                ->waitFor("@date-to")
                ->waitFor("@save-request-button")
                ->click("@date-from")
                ->fillMonth(11)
                ->fillDay(8)
                ->click("@date-to")
                ->fillMonth(11)
                ->fillDay(9)
                ->type("#comment", "Zwolnienie.")
                ->click("@save-request-button")
                ->waitForText("Czeka na akceptację");

            $browser->loginAs($this->technical)
                ->visit(new RequestPage())
                ->waitFor("@single-vacation-request")
                ->click("@single-vacation-request")
                ->waitFor("@vacation-accept-by-technical")
                ->click("@vacation-accept-by-technical")
                ->waitForText("Zaakceptowany przez przełożonego technicznego");

            $browser->loginAs($this->admin)
                ->visit(new RequestPage())
                ->waitFor("@single-vacation-request")
                ->click("@single-vacation-request")
                ->waitFor("@vacation-accept-by-administrative-approval")
                ->click("@vacation-accept-by-administrative-approval")
                ->waitForText("Zaakceptowany przez przełożonego");
        });
    }
}
