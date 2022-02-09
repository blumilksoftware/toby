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

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "year_period_id" => $currentYearPeriod->id,
            "name" => "1/" . $currentYearPeriod->year,
            "type" => VacationType::VACATION->value,
            "state" => VacationRequestState::WAITING_FOR_TECHNICAL,
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
            "state" => VacationRequestState::WAITING_FOR_TECHNICAL,
            "type" => VacationType::VACATION,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/accept-as-technical")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::WAITING_FOR_ADMINISTRATIVE,
        ]);
    }
    public function testAdministrativeApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->createQuietly();
        $administrativeApprover = User::factory()->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => VacationRequestState::WAITING_FOR_ADMINISTRATIVE,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/accept-as-administrative")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::APPROVED,
        ]);
    }
    public function testTechnicalApproverCanRejectVacationRequest(): void
    {
        $user = User::factory()->createQuietly();
        $technicalApprover = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => VacationRequestState::WAITING_FOR_TECHNICAL,
            "type" => VacationType::VACATION,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation-requests/{$vacationRequest->id}/reject")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "state" => VacationRequestState::REJECTED,
        ]);
    }

    public function testUserCannotCreateVacationRequestAtWeekend(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 5)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 6)->toDateString(),
                "comment" => "Vacation at weekend.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("Vacation needs minimum one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtHoliday(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        foreach ($this->polishHolidaysRetriever->getForYearPeriod($currentYearPeriod) as $holiday) {
            $currentYearPeriod->holidays()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 4, 18)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 4, 18)->toDateString(),
                "comment" => "Vacation at holiday.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("Vacation needs minimum one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestIfHeHasPendingVacationRequestInThisRange(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationRequest::factory([
            "type" => VacationType::VACATION->value,
            "state" => VacationRequestState::WAITING_FOR_TECHNICAL,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("You have pending vacation request in this range."),
            ]);
    }
    public function testUserCannotCreateVacationRequestIfHeHasApprovedVacationRequestInThisRange(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();

        VacationRequest::factory([
            "type" => VacationType::VACATION->value,
            "state" => VacationRequestState::APPROVED,
            "from" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("You have approved vacation request in this range."),
            ]);
    }
    public function testUserCannotCreateVacationRequestWithEndDatePriorToTheStartDate(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();
        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 6)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("Vacation needs minimum one day."),
            ]);
    }
    public function testUserCannotCreateVacationRequestAtTheTurnOfTheYear(): void
    {
        $user = User::factory()->createQuietly();
        $currentYearPeriod = YearPeriod::current();
        $nextYearPeriod = $this->createYearPeriod(Carbon::now()->year + 1);
        $this->actingAs($user)
            ->post("/vacation-requests", [
                "type" => VacationType::VACATION->value,
                "from" => Carbon::create($currentYearPeriod->year, 12, 27)->toDateString(),
                "to" => Carbon::create($nextYearPeriod->year, 1, 2)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => trans("The vacation request cannot be created at the turn of the year."),
            ]);
    }
}
