<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Console\Commands\SendVacationRequestSummariesToApprovers;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestsSummaryNotification;
use Toby\States\VacationRequest\Approved;
use Toby\States\VacationRequest\Cancelled;
use Toby\States\VacationRequest\Created;
use Toby\States\VacationRequest\Rejected;
use Toby\States\VacationRequest\WaitingForTechnical;

class SendVacationRequestSummariesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->travelTo(now()->startOfWeek());
    }

    public function testSummariesAreSentOnlyToProperApprovers(): void
    {
        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user, $administrativeApprover], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentOnWeekends(): void
    {
        $this->travelTo(now()->endOfWeek());

        $user = User::factory()->employee()->create();

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNothingSent();
    }

    public function testSummariesAreSentOnlyIfVacationRequestWaitingForActionExists(): void
    {
        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => WaitingForTechnical::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestsSummaryNotification::class);
        Notification::assertNotSentTo([$user], VacationRequestsSummaryNotification::class);
    }

    public function testSummariesAreNotSentIfThereAreNoWaitingForActionVacationRequests(): void
    {
        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $admin = User::factory()->admin()->create();

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => Approved::class]);

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => Cancelled::class]);

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => Rejected::class]);

        VacationRequest::factory()
            ->for($user)
            ->create(["state" => Created::class]);

        $this->artisan(SendVacationRequestSummariesToApprovers::class)
            ->execute();

        Notification::assertNotSentTo([$user, $technicalApprover, $admin], VacationRequestsSummaryNotification::class);
    }
}
