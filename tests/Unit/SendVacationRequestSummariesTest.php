<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Console\Commands\SendVacationRequestSummariesToApprovers;
use Toby\Domain\Notifications\VacationRequestsSummaryNotification;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;

class SendVacationRequestSummariesTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->createCurrentYearPeriod();
        $this->travelTo(now()->startOfWeek());
    }

    public function testSummariesAreSentOnlyToProperApprovers(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user, $administrativeApprover], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentOnWeekends(): void
    {
        $this->travelTo(now()->endOfWeek());
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNothingSent();
    }

    public function testSummariesAreSentOnlyIfVacationRequestWaitingForActionExists(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

        VacationRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentIfThereAreNoWaitingForActionVacationRequests(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

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

        Notification::assertNotSentTo([$user, $technicalApprover, $admin], VacationRequestsSummaryNotification::class);
    }
}
