<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
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

        Bus::fake();
        Notification::fake();

        $this->polishHolidaysRetriever = $this->app->make(PolishHolidaysRetriever::class);
    }

    public function testUserCanSeeVacationRequestsList(): void
    {
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationRequest::factory()
            ->count(10)
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->get("/vacation/requests/me")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("VacationRequest/Index")
                    ->has("requests.data", 10),
            );
    }

    public function testUserCanCreateVacationRequest(): void
    {
        $user = User::factory()->create();

        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "year_period_id" => $currentYearPeriod->id,
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testUserCanCreateVacationRequestOnEmployeeBehalf(): void
    {
        $creator = User::factory()->admin()->create();
        $user = User::factory()->create();

        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($creator)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "creator_id" => $creator->id,
            "year_period_id" => $currentYearPeriod->id,
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testUserCanCreateVacationRequestOnEmployeeBehalfAndSkipAcceptanceFlow(): void
    {
        $creator = User::factory()->admin()->create();
        $user = User::factory()->create();

        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($creator)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
                "flowSkipped" => true,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "creator_id" => $creator->id,
            "year_period_id" => $currentYearPeriod->id,
            "type" => VacationType::Vacation->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYearPeriod->year, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testTechnicalApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/accept-as-technical")
            ->assertSessionHasNoErrors();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(WaitingForAdministrative::class));
    }

    public function testAdministrativeApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $currentYearPeriod = YearPeriod::current();

        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForAdministrative::class,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/accept-as-administrative")
            ->assertSessionHasNoErrors();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Approved::class));
    }

    public function testTechnicalApproverCanRejectVacationRequest(): void
    {
        $user = User::factory()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/reject")
            ->assertSessionHasNoErrors();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Rejected::class));
    }

    public function testUserCannotCreateVacationRequestIfHeExceedsHisVacationLimit(): void
    {
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 3,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
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
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
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
        $user = User::factory()->create();
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
            ->post("/vacation/requests", [
                "user" => $user->id,
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
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("You have pending vacation request in this range."),
            ]);
    }

    public function testUserCannotCreateVacationRequestIfHeHasApprovedVacationRequestInThisRange(): void
    {
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();

        VacationLimit::factory([
            "days" => 20,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Approved::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
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
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();
        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
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
        $user = User::factory()->create();
        $currentYearPeriod = YearPeriod::current();
        $nextYearPeriod = $this->createYearPeriod(Carbon::now()->year + 1);
        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
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
