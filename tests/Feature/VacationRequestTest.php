<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Enums\EmploymentForm;
use Toby\Enums\VacationType;
use Toby\Models\Holiday;
use Toby\Models\Profile;
use Toby\Models\User;
use Toby\Models\VacationLimit;
use Toby\Models\VacationRequest;
use Toby\States\VacationRequest\Approved;
use Toby\States\VacationRequest\Cancelled;
use Toby\States\VacationRequest\Rejected;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

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

        $now = Carbon::now();

        VacationRequest::factory()
            ->count(10)
            ->for($user)
            ->create(["from" => $now]);

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
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testUserCanCreateVacationRequestOnEmployeeBehalf(): void
    {
        $creator = User::factory()->admin()->create();
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($creator)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "creator_id" => $creator->id,
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testUserCanCreateVacationRequestOnEmployeeBehalfAndSkipAcceptanceFlow(): void
    {
        $creator = User::factory()->admin()->create();
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($creator)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
                "flowSkipped" => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "creator_id" => $creator->id,
            "type" => VacationType::Vacation->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testUserCanBulkCreateVacationRequest(): void
    {
        $creator = User::factory()->admin()->create();

        $users = User::factory()
            ->count(3)
            ->has(Profile::factory())
            ->create();

        $currentYear = Carbon::now()->year;

        $this->actingAs($creator)
            ->post("/vacation/requests/bulk", [
                "users" => [$users[0]->id, $users[1]->id, $users[2]->id],
                "type" => VacationType::RemoteWork->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $users[0]->id,
            "creator_id" => $creator->id,
            "type" => VacationType::RemoteWork->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $users[1]->id,
            "creator_id" => $creator->id,
            "type" => VacationType::RemoteWork->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $users[2]->id,
            "creator_id" => $creator->id,
            "type" => VacationType::RemoteWork->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testWhenBulkCreatingOnlyValidatedUsersHaveCreatedRequest(): void
    {
        $creator = User::factory()->admin()->create();
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $invalidUser = User::factory()
            ->has(Profile::factory())
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($creator)
            ->post("/vacation/requests/bulk", [
                "users" => [$user->id, $invalidUser->id],
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
                "flowSkipped" => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "creator_id" => $creator->id,
            "type" => VacationType::Vacation->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);

        $this->assertDatabaseMissing("vacation_requests", [
            "user_id" => $invalidUser->id,
            "creator_id" => $creator->id,
            "type" => VacationType::Vacation->value,
            "state" => Approved::$name,
            "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ]);
    }

    public function testTechnicalApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/accept-as-technical")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(WaitingForAdministrative::class));
    }

    public function testAdministrativeApproverCanApproveVacationRequest(): void
    {
        $user = User::factory()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForAdministrative::class,
        ])
            ->for($user)
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/accept-as-administrative")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Approved::class));
    }

    public function testTechnicalApproverCanRejectVacationRequest(): void
    {
        $user = User::factory()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/reject")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Rejected::class));
    }

    public function testUserCannotCreateVacationRequestIfHeExceedsHisVacationLimit(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 3,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("Vacation limit has been exceeded."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtWeekend(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 5)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 6)->toDateString(),
                "comment" => "Vacation at weekend.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("The request must be for at least one day."),
            ]);
    }

    public function testUserCanCreateNonWorkDayVacationRequestAtWeekend(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Delegation->value,
                "from" => Carbon::create($currentYear, 2, 5)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 6)->toDateString(),
                "comment" => "Delegation at weekend.",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "type" => VacationType::Delegation->value,
            "from" => Carbon::create($currentYear, 2, 5)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 6)->toDateString(),
            "comment" => "Delegation at weekend.",
        ]);
    }

    public function testUserCannotCreateVacationRequestAtHoliday(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        foreach ($this->polishHolidaysRetriever->getForYear($currentYear) as $holiday) {
            Holiday::query()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 4, 18)->toDateString(),
                "to" => Carbon::create($currentYear, 4, 18)->toDateString(),
                "comment" => "Vacation at holiday.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("The request must be for at least one day."),
            ]);
    }

    public function testUserCanCreateNonWorkDayVacationAtHoliday(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        foreach ($this->polishHolidaysRetriever->getForYear($currentYear) as $holiday) {
            Holiday::query()->create([
                "name" => $holiday["name"],
                "date" => $holiday["date"],
            ]);
        }

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Delegation->value,
                "from" => Carbon::create($currentYear, 4, 18)->toDateString(),
                "to" => Carbon::create($currentYear, 4, 18)->toDateString(),
                "comment" => "Delegation at holiday.",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("vacation_requests", [
            "user_id" => $user->id,
            "type" => VacationType::Delegation->value,
            "from" => Carbon::create($currentYear, 4, 18)->toDateString(),
            "to" => Carbon::create($currentYear, 4, 18)->toDateString(),
            "comment" => "Delegation at holiday.",
        ]);
    }

    public function testUserCannotCreateVacationRequestIfHeHasPendingVacationRequestInThisRange(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
            "from" => Carbon::create($currentYear, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("You have a pending request in this date range."),
            ]);
    }

    public function testUserCannotCreateVacationRequestIfHeHasApprovedVacationRequestInThisRange(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Approved::class,
            "from" => Carbon::create($currentYear, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYear, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 1)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 4)->toDateString(),
                "comment" => "Another comment for the another vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("You have an approved request in this date range."),
            ]);
    }

    public function testUserCannotCreateVacationRequestWithEndDatePriorToTheStartDate(): void
    {
        $user = User::factory()->create();
        $currentYear = Carbon::now()->year;

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 6)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("The request must be for at least one day."),
            ]);
    }

    public function testUserCannotCreateVacationRequestAtTheTurnOfTheYear(): void
    {
        $user = User::factory()->create();
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $user->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 12, 27)->toDateString(),
                "to" => Carbon::create($nextYear, 1, 2)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertSessionHasErrors([
                "vacationRequest" => __("The request cannot be created at the turn of the year."),
            ]);
    }

    public function testEmployeeCanSeeOnlyHisVacationRequests(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/vacation/requests")
            ->assertRedirect("/vacation/requests/me");
    }

    public function testEmployeeCannotCreateVacationRequestForAnotherEmployee(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $currentYear = Carbon::now()->year;

        $this->actingAs($user)
            ->post("/vacation/requests", [
                "user" => $anotherUser->id,
                "type" => VacationType::Vacation->value,
                "from" => Carbon::create($currentYear, 2, 7)->toDateString(),
                "to" => Carbon::create($currentYear, 2, 11)->toDateString(),
                "comment" => "Comment for the vacation request.",
            ])
            ->assertForbidden();
    }

    public function testEmployeeCanCancelVacationRequestWithWaitingForAdministrativeStatus(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForAdministrative::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests/{$vacationRequest->id}/cancel")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Cancelled::class));
    }

    public function testEmployeeCannotCancelVacationRequestWithApprovedStatus(): void
    {
        $user = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => Approved::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests/{$vacationRequest->id}/cancel")
            ->assertForbidden();
    }

    public function testTechnicalApproverCanCancelVacationRequestWithApprovedStatus(): void
    {
        $user = User::factory()->technicalApprover()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => Approved::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests/{$vacationRequest->id}/cancel")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Cancelled::class));
    }

    public function testEmployeeCanCancelRemoteWorkEvenWithApprovedStatus(): void
    {
        $user = User::factory()->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => Approved::class,
            "type" => VacationType::RemoteWork,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/vacation/requests/{$vacationRequest->id}/cancel")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Cancelled::class));
    }

    public function testAdministrativeApproverCanCancelVacationRequestWithApprovedStatus(): void
    {
        $user = User::factory()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => Approved::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/vacation/requests/{$vacationRequest->id}/cancel")
            ->assertRedirect();

        $vacationRequest->refresh();

        $this->assertTrue($vacationRequest->state->equals(Cancelled::class));
    }

    public function testEmployeeCanDownloadHisVacationRequestAsPdf(): void
    {
        Storage::fake();
        $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($user)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->get("/vacation/requests/{$vacationRequest->id}/download")
            ->assertSuccessful();
    }

    public function testEmployeeCannotDownloadAnotherEmployeesVacationRequestAsPdf(): void
    {
        Storage::fake();
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $currentYear = Carbon::now()->year;

        VacationLimit::factory([
            "days" => 20,
            "year" => $currentYear,
        ])
            ->for($anotherUser)
            ->create();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "state" => WaitingForTechnical::class,
            "type" => VacationType::Vacation,
        ])
            ->for($anotherUser)
            ->create();

        $this->actingAs($user)
            ->get("/vacation/requests/{$vacationRequest->id}/download")
            ->assertForbidden();
    }

    public function testCorrectVacationTypesAreAvailableForEmployee(): void
    {
        $employee = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $this->actingAs($employee)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $employee->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Urlop wypoczynkowy", "value" => "vacation"],
                ["label" => "Zwolnienie lekarskie", "value" => "sick_vacation"],
                ["label" => "Urlop okolicznościowy", "value" => "special_vacation"],
                ["label" => "Urlop bezpłatny", "value" => "unpaid_vacation"],
                ["label" => "Urlop na żądanie", "value" => "vacation_on_request"],
                ["label" => "Urlop szkoleniowy", "value" => "training_vacation"],
                ["label" => "Opieka nad dzieckiem (art. 188 kp)", "value" => "childcare_vacation"],
                ["label" => "Wolontariat", "value" => "volunteering_vacation"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForTechnicalApprover(): void
    {
        $technicalApprover = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->technicalApprover()
            ->create();

        $this->actingAs($technicalApprover)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $technicalApprover->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Urlop wypoczynkowy", "value" => "vacation"],
                ["label" => "Zwolnienie lekarskie", "value" => "sick_vacation"],
                ["label" => "Urlop okolicznościowy", "value" => "special_vacation"],
                ["label" => "Odbiór za święto", "value" => "time_in_lieu"],
                ["label" => "Urlop bezpłatny", "value" => "unpaid_vacation"],
                ["label" => "Urlop na żądanie", "value" => "vacation_on_request"],
                ["label" => "Urlop szkoleniowy", "value" => "training_vacation"],
                ["label" => "Opieka nad dzieckiem (art. 188 kp)", "value" => "childcare_vacation"],
                ["label" => "Wolontariat", "value" => "volunteering_vacation"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForAdministrativeApprover(): void
    {
        $administrativeApprover = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->administrativeApprover()
            ->create();

        $this->actingAs($administrativeApprover)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $administrativeApprover->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Urlop wypoczynkowy", "value" => "vacation"],
                ["label" => "Zwolnienie lekarskie", "value" => "sick_vacation"],
                ["label" => "Urlop okolicznościowy", "value" => "special_vacation"],
                ["label" => "Delegacja", "value" => "delegation"],
                ["label" => "Odbiór za święto", "value" => "time_in_lieu"],
                ["label" => "Urlop bezpłatny", "value" => "unpaid_vacation"],
                ["label" => "Urlop na żądanie", "value" => "vacation_on_request"],
                ["label" => "Urlop szkoleniowy", "value" => "training_vacation"],
                ["label" => "Opieka nad dzieckiem (art. 188 kp)", "value" => "childcare_vacation"],
                ["label" => "Wolontariat", "value" => "volunteering_vacation"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForAdmin(): void
    {
        $admin = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->admin()
            ->create();

        $this->actingAs($admin)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $admin->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Urlop wypoczynkowy", "value" => "vacation"],
                ["label" => "Zwolnienie lekarskie", "value" => "sick_vacation"],
                ["label" => "Urlop okolicznościowy", "value" => "special_vacation"],
                ["label" => "Delegacja", "value" => "delegation"],
                ["label" => "Odbiór za święto", "value" => "time_in_lieu"],
                ["label" => "Urlop bezpłatny", "value" => "unpaid_vacation"],
                ["label" => "Urlop na żądanie", "value" => "vacation_on_request"],
                ["label" => "Urlop szkoleniowy", "value" => "training_vacation"],
                ["label" => "Opieka nad dzieckiem (art. 188 kp)", "value" => "childcare_vacation"],
                ["label" => "Wolontariat", "value" => "volunteering_vacation"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForCommissionContract(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::CommissionContract]))
            ->create();

        $this->actingAs($user)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $user->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Nieobecność", "value" => "absence"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForB2bContract(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::B2bContract]))
            ->create();

        $this->actingAs($user)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $user->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Nieobecność", "value" => "absence"],
            ]);
    }

    public function testCorrectVacationTypesAreAvailableForBoardMemberContract(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::BoardMemberContract]))
            ->create();

        $this->actingAs($user)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $user->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Nieobecność", "value" => "absence"],
            ]);
    }

    public function testEmployeeWithPermissionToCreateOnBehalfSeesAllVacationTypesForEmployee(): void
    {
        $employeeWithPermission = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $employeeWithPermission->givePermissionTo("createRequestsOnBehalfOfEmployee");

        $targetEmployee = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $this->actingAs($employeeWithPermission)
            ->post("/api/vacation/get-available-vacation-types", [
                "user" => $targetEmployee->id,
            ])
            ->assertOk()
            ->assertJson([
                ["label" => "Praca zdalna", "value" => "remote_work"],
                ["label" => "Urlop wypoczynkowy", "value" => "vacation"],
                ["label" => "Zwolnienie lekarskie", "value" => "sick_vacation"],
                ["label" => "Urlop okolicznościowy", "value" => "special_vacation"],
                ["label" => "Delegacja", "value" => "delegation"],
                ["label" => "Odbiór za święto", "value" => "time_in_lieu"],
                ["label" => "Urlop bezpłatny", "value" => "unpaid_vacation"],
                ["label" => "Urlop na żądanie", "value" => "vacation_on_request"],
                ["label" => "Urlop szkoleniowy", "value" => "training_vacation"],
                ["label" => "Opieka nad dzieckiem (art. 188 kp)", "value" => "childcare_vacation"],
                ["label" => "Wolontariat", "value" => "volunteering_vacation"],
            ]);
    }
}
