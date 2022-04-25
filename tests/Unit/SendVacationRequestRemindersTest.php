<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\Notifications\VacationRequestWaitsForApprovalNotification;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Console\Commands\SendVacationRequestRemindersToApprovers;

class SendVacationRequestRemindersTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCurrentYearPeriod();

        Notification::fake();
    }

    public function testReminderIsSentIfItsBeenThreeDaysSinceTheUpdate(): void
    {
        $currentYearPeriod = YearPeriod::current();
        $now = Carbon::today();
        $this->travelTo($now);

        $user = User::factory()->create();
        $technicalApprover = User::factory()
            ->technicalApprover()
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->travelTo($now->addDays(3));

        $this->artisan(SendVacationRequestRemindersToApprovers::class);

        Notification::assertSentTo([$technicalApprover], VacationRequestWaitsForApprovalNotification::class);
    }

    public function testReminderIsSentIfItsBeenAnotherThreeDaysSinceTheUpdate(): void
    {
        $currentYearPeriod = YearPeriod::current();
        $now = Carbon::today();
        $this->travelTo($now);

        $user = User::factory()->create();
        $technicalApprover = User::factory()
            ->technicalApprover()
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->travelTo($now->addDays(6));

        $this->artisan(SendVacationRequestRemindersToApprovers::class);

        Notification::assertSentTo([$technicalApprover], VacationRequestWaitsForApprovalNotification::class);
    }

    public function testReminderIsNotSentIfItHasntBeenThreeDays(): void
    {
        $currentYearPeriod = YearPeriod::current();
        $now = Carbon::today();
        $this->travelTo($now);

        $user = User::factory()->create();
        $technicalApprover = User::factory()
            ->technicalApprover()
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForTechnical::class,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->travelTo($now->addDays(2));

        $this->artisan(SendVacationRequestRemindersToApprovers::class);

        Notification::assertNotSentTo([$technicalApprover], VacationRequestWaitsForApprovalNotification::class);
    }

    public function testReminderIsSentToProperApprover(): void
    {
        $currentYearPeriod = YearPeriod::current();
        $now = Carbon::today();
        $this->travelTo($now);

        $user = User::factory()->create();
        $adminApprover = User::factory()
            ->administrativeApprover()
            ->create();
        $technicalApprover = User::factory()
            ->technicalApprover()
            ->create();

        VacationRequest::factory([
            "type" => VacationType::Vacation->value,
            "state" => WaitingForAdministrative::class,
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->travelTo($now->addDays(3));

        $this->artisan(SendVacationRequestRemindersToApprovers::class);

        Notification::assertSentTo([$adminApprover], VacationRequestWaitsForApprovalNotification::class);
        Notification::assertNotSentTo([$technicalApprover, $user], VacationRequestWaitsForApprovalNotification::class);
    }
}
