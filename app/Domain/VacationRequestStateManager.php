<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Contracts\Events\Dispatcher;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\States\VacationRequest\AcceptedByAdministrative;
use Toby\States\VacationRequest\AcceptedByTechnical;
use Toby\States\VacationRequest\Approved;
use Toby\States\VacationRequest\Cancelled;
use Toby\States\VacationRequest\Rejected;
use Toby\States\VacationRequest\VacationRequestState;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

class VacationRequestStateManager
{
    public function __construct(
        protected Dispatcher $dispatcher,
    ) {}

    public function markAsCreated(VacationRequest $vacationRequest): void
    {
        $this->createActivity($vacationRequest, null, $vacationRequest->state, $vacationRequest->creator);
    }

    public function approve(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, Approved::class, $user);
    }

    public function reject(VacationRequest $vacationRequest, User $user): void
    {
        $this->changeState($vacationRequest, Rejected::class, $user);
    }

    public function cancel(VacationRequest $vacationRequest, User $user): void
    {
        $this->changeState($vacationRequest, Cancelled::class, $user);
    }

    public function acceptAsTechnical(VacationRequest $vacationRequest, User $user): void
    {
        $this->changeState($vacationRequest, AcceptedByTechnical::class, $user);
    }

    public function acceptAsAdministrative(VacationRequest $vacationRequest, User $user): void
    {
        $this->changeState($vacationRequest, AcceptedByAdministrative::class, $user);
    }

    public function waitForTechnical(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, WaitingForTechnical::class);
    }

    public function waitForAdministrative(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, WaitingForAdministrative::class);
    }

    protected function changeState(VacationRequest $vacationRequest, string $state, ?User $user = null): void
    {
        $previousState = $vacationRequest->state;
        $vacationRequest->state->transitionTo($state);
        $vacationRequest->save();

        $this->createActivity($vacationRequest, $previousState, $vacationRequest->state, $user);
    }

    protected function createActivity(
        VacationRequest $vacationRequest,
        ?VacationRequestState $from,
        VacationRequestState $to,
        ?User $user = null,
    ): void {
        $vacationRequest->activities()->create([
            "from" => $from,
            "to" => $to,
            "user_id" => $user?->id,
        ]);
    }
}
