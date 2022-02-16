<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationRequestTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected PolishHolidaysRetriever $polishHolidaysRetriever;

    protected function setUp(): void
    {
        parent::setUp();

        $this->polishHolidaysRetriever = $this->app->make(PolishHolidaysRetriever::class);
    }

    public function testUserCanSeeVacationRequestsList(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationRequest::factory()
            ->count(10)
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->get("/vacation-requests")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("VacationRequest/Index")
                    ->has("requests.data", 10),
            );
    }

    public function testUserCanCreateVacationRequest(): void
    {
        $user = User::factory()->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "year_period_id" => $currentYearPeriod->id,
            "name" => "1/" . $currentYearPeriod->year,
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::WaitingForTechnical,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testTechnicalApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->createQuietly();
        $technicalApprover = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => VacationRequestState::WaitingForTechnical,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/accept-as-technical")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::WaitingForAdministrative,
        ]);
    }

    public function testAdministrativeApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->createQuietly();
        $administrativeApprover = User::factory()->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => VacationRequestState::WaitingForAdministrative,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/accept-as-administrative")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::Approved,
        ]);
    }

    public function testTechnicalApproverCanRejectVacationRequest(): void
    {
        $user = User::factory()->createQuietly();
        $technicalApprover = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        $vacationLimit = VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $vacationRequest = VacationRequest::factory([
            "state" => VacationRequestState::WaitingForTechnical,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/reject")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::Rejected,
        ]);
    }

    public function testUserCannotCreateVacationRequestIfHeExceedsHisVacationLimit(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 3,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("Vacation limit has been exceeded."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtWeekend(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 5)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 6)->toDateString(),
                "comment" => "Vacation at weekend.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("Vacation needs minimum one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtHoliday(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        foreach ($this->polishHolidaysRetriever->getForYearPeriod($currentYearPeriod) as $holiday) {
            $currentYearPeriod->holidays()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 4, 18)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 4, 18)->toDateString(),
                "comment" => "Vacation at holiday.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("Vacation needs minimum one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestIfHeHasPendingVacationRequestInThisRange(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::WaitingForTechnical,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("You have pending vacation request in this range."),
            ])
        ;
    }

    public function testUserCannotCreateVacationRequestIfHeHasApprovedVacationRequestInThisRange(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        $vacationLimit = VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Approved,
            "from" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("You have approved vacation request in this range."),
            ]);
    }

    public function testUserCannotCreateVacationRequestWithEndDatePriorToTheStartDate(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();
        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 6)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("Vacation needs minimum one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtTheTurnOfTheYear(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();
        $nextYearPeriod = $this->createYearPeriod(Carbon::now()->year + 1);
        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 12, 27)->toDateString(),
                "to" => Carbon::create($nextYearPeriod->year, 1, 2)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("The vacation request cannot be created at the turn of the year."),
            ]);
    }
}
