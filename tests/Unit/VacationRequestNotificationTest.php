<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Actions\VacationRequest\RejectAction;
use Toby\Domain\Actions\VacationRequest\WaitForTechApprovalAction;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\Notifications\VacationRequestStatusChangedNotification;
use Toby\Domain\Notifications\VacationRequestWaitsForApprovalNotification;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationRequestNotificationTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCurrentYearPeriod();
    }

    public function testAfterChangingVacationRequestStateNotificationAreSentToUsers(): void
    {
        Notification::fake();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $waitForTechApprovalAction = $this->app->make(WaitForTechApprovalAction::class);

        $waitForTechApprovalAction->execute($vacationRequest);

        Notification::assertSentTo([$technicalApprover, $admin], VacationRequestWaitsForApprovalNotification::class);
        Notification::assertNotSentTo([$user, $administrativeApprover], VacationRequestWaitsForApprovalNotification::class);
    }

    public function testNotificationIsSentOnceToUser(): void
    {
        Notification::fake();

        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($administrativeApprover)
            ->for($currentYearPeriod)
            ->create();

        $rejectAction = $this->app->make(RejectAction::class);

        $rejectAction->execute($vacationRequest, $technicalApprover);

        Notification::assertSentTo([$technicalApprover, $admin, $administrativeApprover], VacationRequestStatusChangedNotification::class);
        Notification::assertSentTimes(VacationRequestStatusChangedNotification::class, 3);
    }
}
