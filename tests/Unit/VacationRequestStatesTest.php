<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationRequestStateManager;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationRequestStatesTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected VacationRequestStateManager $stateManager;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->stateManager = $this->app->make(VacationRequestStateManager::class);

        $this->createCurrentYearPeriod();
    }

    public function testAfterCreatingVacationRequestOfTypeVacationItTransitsToProperState(): void
    {
        $user = User::factory()->createQuietly();

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

        $this->assertEquals(VacationRequestState::WaitingForTechnical, $vacationRequest->state);
    }

    public function testAfterCreatingVacationRequestOfTypeSickVacationItTransitsToProperState(): void
    {
        $user = User::factory()->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Sick->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->approve($vacationRequest);

        $this->assertEquals(VacationRequestState::Approved, $vacationRequest->state);
    }

    public function testAfterCreatingVacationRequestOfTypeTimeInLieuItTransitsToProperState(): void
    {
        $user = User::factory()->createQuietly();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::TimeInLieu->value,
            "state" => VacationRequestState::Created,
            "from" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->approve($vacationRequest);

        $this->assertEquals(VacationRequestState::Approved, $vacationRequest->state);
    }
}
