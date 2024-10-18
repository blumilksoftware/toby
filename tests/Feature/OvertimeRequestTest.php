<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Enums\EmploymentForm;
use Toby\Enums\SettlementType;
use Toby\Models\OvertimeRequest;
use Toby\Models\Profile;
use Toby\Models\User;
use Toby\States\OvertimeRequest\Approved;
use Toby\States\OvertimeRequest\Cancelled;
use Toby\States\OvertimeRequest\Rejected;
use Toby\States\OvertimeRequest\Settled;
use Toby\States\OvertimeRequest\WaitingForTechnical;

class OvertimeRequestTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeOvertimeRequestsList(): void
    {
        $user = User::factory()
            ->employee()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $year = Carbon::now();

        OvertimeRequest::factory()
            ->count(10)
            ->for($user)
            ->create(["from" => $year]);

        $this->actingAs($user)
            ->get("/overtime/requests/me")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("OvertimeRequest/Index")
                    ->has("requests.data", 10),
            );
    }

    public function testUserWithEmploymentContractCanCreateOvertimeRequest(): void
    {
        $user = User::factory()
            ->employee()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $year = Carbon::now()->year;

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 20)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 23)->format("Y-m-d H:i"),
                "comment" => "Comment for the overtime request.",
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "user_id" => $user->id,
            "settlement_type" => SettlementType::Money->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($year, 2, 7, 20)->format("Y-m-d H:i"),
            "to" => Carbon::create($year, 2, 7, 23)->format("Y-m-d H:i"),
            "hours" => 3,
            "comment" => "Comment for the overtime request.",
        ]);
    }

    public function testUserWithB2bContractCannotCreateOvertimeRequest(): void
    {
        $user = User::factory()
            ->employee()
            ->has(Profile::factory(["employment_form" => EmploymentForm::B2bContract]))
            ->create();

        $this->actingAs($user)
            ->get("/overtime/requests/create")
            ->assertStatus(403);
    }

    public function testUserWithBoardMemberContractCannotCreateOvertimeRequest(): void
    {
        $user = User::factory()
            ->employee()
            ->has(Profile::factory(["employment_form" => EmploymentForm::BoardMemberContract]))
            ->create();

        $this->actingAs($user)
            ->get("/overtime/requests/create")
            ->assertStatus(403);
    }

    public function testUserWithCommissionContractCannotCreateOvertimeRequest(): void
    {
        $user = User::factory()
            ->employee()
            ->has(Profile::factory(["employment_form" => EmploymentForm::CommissionContract]))
            ->create();

        $this->actingAs($user)
            ->get("/overtime/requests/create")
            ->assertStatus(403);
    }

    public function testTechnicalApproverCanApproveOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::$name]);

        $this->actingAs($technicalApprover)
            ->post("/overtime/requests/{$overtimeRequest->id}/accept-as-technical")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Approved::$name,
        ]);
    }

    public function testEmployeeCannotApproveOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();
        $approver = User::factory()->employee()->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::$name]);

        $this->actingAs($approver)
            ->post("/overtime/requests/{$overtimeRequest->id}/accept-as-technical")
            ->assertStatus(403);

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => WaitingForTechnical::$name,
        ]);
    }

    public function testTechnicalApproverCanRejectOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::$name]);

        $this->actingAs($technicalApprover)
            ->post("/overtime/requests/{$overtimeRequest->id}/reject")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Rejected::$name,
        ]);
    }

    public function testEmployeeCanCancelOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/cancel")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Cancelled::$name,
        ]);
    }

    public function testEmployeeCannotCreateOvertimeRequestIfHeHasPendingOvertimeRequestInThisRange(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $year = Carbon::now()->year;

        OvertimeRequest::factory([
            "state" => WaitingForTechnical::class,
            "from" => Carbon::create($year, 2, 7, 19)->format("Y-m-d H:i"),
            "to" => Carbon::create($year, 2, 7, 22)->format("Y-m-d H:i"),
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 21)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 23)->format("Y-m-d H:i"),
            ])
            ->assertSessionHasErrors([
                "overtimeRequest" => __("You have a pending request in this date range."),
            ]);

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 18)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 22)->format("Y-m-d H:i"),
            ])
            ->assertSessionHasErrors([
                "overtimeRequest" => __("You have a pending request in this date range."),
            ]);

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 21)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 22)->format("Y-m-d H:i"),
            ])
            ->assertSessionHasErrors([
                "overtimeRequest" => __("You have a pending request in this date range."),
            ]);

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 6, 21)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 8, 22)->format("Y-m-d H:i"),
            ])
            ->assertSessionHasErrors([
                "overtimeRequest" => __("You have a pending request in this date range."),
            ]);
    }

    public function testEmployeeCannotCreateOvertimeRequestIfHeHasSettledOvertimeRequestInThisRange(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $year = Carbon::now()->year;

        OvertimeRequest::factory([
            "state" => Settled::class,
            "from" => Carbon::create($year, 2, 7, 20)->format("Y-m-d H:i"),
            "to" => Carbon::create($year, 2, 7, 22)->format("Y-m-d H:i"),
        ])
            ->for($user)
            ->create();

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 21)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 23)->format("Y-m-d H:i"),
            ])
            ->assertSessionHasErrors([
                "overtimeRequest" => __("You have a pending request in this date range."),
            ]);
    }

    public function testTechnicalApproverCanSettleOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => Approved::$name]);

        $this->actingAs($technicalApprover)
            ->post("/overtime/requests/{$overtimeRequest->id}/settle")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Settled::$name,
        ]);
    }

    public function testEmployeeCanSettleOvertimeRequest(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => Approved::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/settle")
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Settled::$name,
        ]);
    }

    public function testEmployeeCannotSettleOvertimeRequestIfItIsNotApproved(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/settle")
            ->assertStatus(403);

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => WaitingForTechnical::$name,
        ]);
    }

    public function testEmployeeCannotSettleOvertimeRequestIfItIsRejected(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => Rejected::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/settle")
            ->assertStatus(403);

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Rejected::$name,
        ]);
    }

    public function testEmployeeCannotCancelOvertimeRequestIfItIsSettled(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => Settled::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/cancel")
            ->assertStatus(403);

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Settled::$name,
        ]);
    }

    public function testEmployeeCannotCancelOvertimeRequestIfItIsApproved(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $overtimeRequest = OvertimeRequest::factory()
            ->for($user)
            ->create(["state" => Approved::$name]);

        $this->actingAs($user)
            ->post("/overtime/requests/{$overtimeRequest->id}/cancel")
            ->assertStatus(403);

        $this->assertDatabaseHas("overtime_requests", [
            "id" => $overtimeRequest->id,
            "state" => Approved::$name,
        ]);
    }

    public function testOvertimeRequestHoursAreRoundedUp(): void
    {
        $user = User::factory()
            ->has(Profile::factory(["employment_form" => EmploymentForm::EmploymentContract]))
            ->create();

        $year = Carbon::now()->year;

        $this->actingAs($user)
            ->post("/overtime/requests", [
                "user" => $user->id,
                "type" => SettlementType::Money->value,
                "from" => Carbon::create($year, 2, 7, 20)->format("Y-m-d H:i"),
                "to" => Carbon::create($year, 2, 7, 23, 5)->format("Y-m-d H:i"),
                "comment" => "Comment for the overtime request.",
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas("overtime_requests", [
            "user_id" => $user->id,
            "settlement_type" => SettlementType::Money->value,
            "state" => WaitingForTechnical::$name,
            "from" => Carbon::create($year, 2, 7, 20)->format("Y-m-d H:i"),
            "to" => Carbon::create($year, 2, 7, 23, 5)->format("Y-m-d H:i"),
            "hours" => 4,
            "comment" => "Comment for the overtime request.",
        ]);
    }
}
