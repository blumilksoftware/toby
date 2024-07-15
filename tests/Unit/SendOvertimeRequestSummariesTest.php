<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Console\Commands\SendOvertimeRequestSummariesToApprovers;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\YearPeriod;
use Toby\Notifications\OvertimeRequestsSummaryNotification;
use Toby\States\OvertimeRequest\Approved;
use Toby\States\OvertimeRequest\Cancelled;
use Toby\States\OvertimeRequest\Created;
use Toby\States\OvertimeRequest\Rejected;
use Toby\States\OvertimeRequest\Settled;
use Toby\States\OvertimeRequest\WaitingForTechnical;

class SendOvertimeRequestSummariesTest extends TestCase
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

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendOvertimeRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], OvertimeRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user, $administrativeApprover], OvertimeRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentOnWeekends(): void
    {
        $this->travelTo(now()->endOfWeek());
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendOvertimeRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNothingSent();
    }

    public function testSummariesAreSentOnlyIfOvertimeRequestWaitingForActionExists(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendOvertimeRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], OvertimeRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user], OvertimeRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentIfThereAreNoWaitingForActionOvertimeRequests(): void
    {
        $currentYearPeriod = YearPeriod::current();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Approved::class]);

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Cancelled::class]);

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Rejected::class]);

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Created::class]);

        OvertimeRequest::factory()
            ->for($user)
            ->for($currentYearPeriod)
            ->create(["state" => Settled::class]);

        $this->artisan(SendOvertimeRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNotSentTo([$user, $technicalApprover, $admin], OvertimeRequestsSummaryNotification::class);
    }
}
