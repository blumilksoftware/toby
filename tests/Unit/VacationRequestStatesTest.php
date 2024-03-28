<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\InteractsWithYearPeriods;
use Toby\Domain\VacationRequestStateManager;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;
use Toby\States\VacationRequest\Approved;
use Toby\States\VacationRequest\Created;
use Toby\States\VacationRequest\WaitingForTechnical;

class VacationRequestStatesTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithYearPeriods;

    protected VacationRequestStateManager $stateManager;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Bus::fake();

        $this->stateManager = $this->app->make(VacationRequestStateManager::class);

        $this->createCurrentYearPeriod();
    }

    public function testAfterCreatingVacationRequestOfTypeVacationItTransitsToProperState(): void
    {
        $user = User::factory()->create();

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

        $this->stateManager->waitForTechnical($vacationRequest);

        $this->assertTrue($vacationRequest->state->equals(WaitingForTechnical::class));
    }

    public function testAfterCreatingVacationRequestOfTypeSickVacationItTransitsToProperState(): void
    {
        $user = User::factory()->create();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::Sick->value,
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 1)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 4)->toDateString(),
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->approve($vacationRequest);

        $this->assertTrue($vacationRequest->state->equals(Approved::class));
    }

    public function testAfterCreatingVacationRequestOfTypeTimeInLieuItTransitsToProperState(): void
    {
        $user = User::factory()->create();

        $currentYearPeriod = YearPeriod::current();

        /** @var VacationRequest $vacationRequest */
        $vacationRequest = VacationRequest::factory([
            "type" => VacationType::TimeInLieu->value,
            "state" => Created::class,
            "from" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
            "to" => Carbon::create($currentYearPeriod->year, 2, 2)->toDateString(),
        ])
            ->for($user)
            ->for($currentYearPeriod)
            ->create();

        $this->stateManager->approve($vacationRequest);

        $this->assertTrue($vacationRequest->state->equals(Approved::class));
    }
}
