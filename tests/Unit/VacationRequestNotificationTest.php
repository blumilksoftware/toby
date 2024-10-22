<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Toby\Actions\VacationRequest\RejectAction;
use Toby\Actions\VacationRequest\WaitForTechApprovalAction;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestStatusChangedNotification;
use Toby\Notifications\VacationRequestWaitsForApprovalNotification;
use Toby\States\VacationRequest\Created;
use Toby\States\VacationRequest\WaitingForTechnical;

class VacationRequestNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function testAfterChangingVacationRequestStateNotificationAreSentToUsers(): void
    {
        Notification::fake();

        $user = User::factory()->employee()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();
        $admin = User::factory()->admin()->create();

        $year = Carbon::now()->year;

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => Created::class,
            "from" => Carbon::create($year, 2, 1)->toDateString(),
            "to" => Carbon::create($year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
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

        $year = Carbon::now()->year;

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
            "from" => Carbon::create($year, 2, 1)->toDateString(),
            "to" => Carbon::create($year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($administrativeApprover)
            ->create();

        $rejectAction = $this->app->make(RejectAction::class);

        $rejectAction->execute($vacationRequest, $technicalApprover);

        Notification::assertSentTo([$technicalApprover, $admin, $administrativeApprover], VacationRequestStatusChangedNotification::class);
        Notification::assertSentTimes(VacationRequestStatusChangedNotification::class, 3);
    }
}
