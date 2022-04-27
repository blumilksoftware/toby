<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestsSummaryNotification;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Console\Commands\SendVacationRequestSummariesToApprovers;

class SendVacationRequestSummariesTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->createCurrentYearPeriod();
    }

    public function testSummariesAreSentOnlyToProperApprovers(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->create();
        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->create();
        $admin = User::factory([
            "role" => Role::Administrator,
        ])->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreSentOnlyIfVacationRequestWaitingForActionExists(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->create();
        $admin = User::factory([
            "role" => Role::Administrator,
        ])->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSendIfTherAreNoWaitingForActionVacationRequests(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory([
            "role" => Role::Employee,
        ])->create();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->create();
        $admin = User::factory([
            "role" => Role::Administrator,
        ])->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Approved::class]);

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Cancelled::class]);

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Rejected::class]);

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Created::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNotSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
    }
}
