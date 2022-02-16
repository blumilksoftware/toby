<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\Role;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\Notifications\VacationRequestNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationRequestNotificationTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected VacationRequestStateManager $stateManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateManager = $this->app->make(VacationRequestStateManager::class);

        $this->createCurrentYearPeriod();
    }

    public function testAfterChangingVacationRequestStateNotificationAreSentToUsers(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->createQuietly();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->createQuietly();
        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->waitForTechnical($vacationRequest);

        Notification::assertSentTo([$user, $technicalApprover, $administrativeApprover], VacationRequestNotification::class);
    }

    public function testAfterChangingVacationRequestStateNotificationIsNotSentToAnotherEmployee(): void
    {
        Notification::fake();

        $user = User::factory([
            "role" => Role::Employee,
        ])->createQuietly();
        $anotherUser = User::factory([
            "role" => Role::Employee,
        ])->createQuietly();
        $technicalApprover = User::factory([
            "role" => Role::TechnicalApprover,
        ])->createQuietly();
        $administrativeApprover = User::factory([
            "role" => Role::AdministrativeApprover,
        ])->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
            "comment" => "Comment for the vacation request.",
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->waitForTechnical($vacationRequest);

        Notification::assertSentTo([$user, $technicalApprover, $administrativeApprover], VacationRequestNotification::class);
        Notification::assertNotSentTo([$anotherUser], VacationRequestNotification::class);
    }
}
