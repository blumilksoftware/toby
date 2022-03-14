<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationRequestTest extends DuskTestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::createFromDate(2022, 1, 1));
        $this->createCurrentYearPeriod();

        $currentYearPeriod = YearPeriod::query()->where("year", 2022)->first();

        $this->employee = User::factory([
            "first_name" => "Jan",
            "last_name" => "Kowalski",
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "programista",
            "role" => Role::Employee,
            "employment_date" => Carbon::createFromDate(2022, 01, 03),
            "remember_token" => Str::random(10),
        ])
            ->create();


        VacationLimit::factory([
            "days" => 26,
        ])
            ->for($currentYearPeriod)
            ->for($this->employee)
            ->create();
    }

    public function testUserCanCreateVacationRequest(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->employee)
                ->visit(new HomePage())
                ->click("@my-vacation-requests-menu-item")
                ->waitForLocation("/vacation-requests/me")
                ->click("@create-vacation-request-button")
                ->waitForLocation("/vacation-requests/create")
                ->assertSeeIn("@name-input", $this->employee->fullName)
                ->click("@type-select")
                ->assertVisible("@type-options")
                ->click("@vacation")
                ->click("@date-from-input")
                ->fillMonth("2")
                ->fillDay("22")
//                ->clickAndWaitForReload("@save-button");
                ->press("Zapisz");

//                ->waitForLocation("/vacation-requests/1")
//                ->screenshot("xd")
//            ;
        });
        $vacationRequest = VacationRequest::query()->first();
        dd($vacationRequest);

    }
}
