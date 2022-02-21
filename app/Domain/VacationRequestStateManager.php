<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Events\Dispatcher;
use Toby\Domain\Events\VacationRequestAcceptedByAdministrative;
use Toby\Domain\Events\VacationRequestAcceptedByTechnical;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\Events\VacationRequestCancelled;
use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Events\VacationRequestStateChanged;
use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\VacationRequestState;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestStateManager
{
    public function __construct(
        protected Auth $auth,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function markAsCreated(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->fireStateChangedEvent($vacationRequest, null, $vacationRequest->state, $user);

        $this->dispatcher->dispatch(new VacationRequestCreated($vacationRequest));
    }

    public function approve(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, Approved::class, $user);

        $this->dispatcher->dispatch(new VacationRequestApproved($vacationRequest));
    }

    public function reject(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, Rejected::class, $user);
    }

    public function cancel(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, Cancelled::class, $user);

        $this->dispatcher->dispatch(new VacationRequestCancelled($vacationRequest));
    }

    public function acceptAsTechnical(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, AcceptedByTechnical::class, $user);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByTechnical($vacationRequest));
    }

    public function acceptAsAdministrative(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, AcceptedByAdministrative::class, $user);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByAdministrative($vacationRequest));
    }

    public function waitForTechnical(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, WaitingForTechnical::class, $user);
    }

    public function waitForAdministrative(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->changeState($vacationRequest, WaitingForAdministrative::class, $user);
    }

    protected function changeState(VacationRequest $vacationRequest, string $state, ?User $user = null): void
    {
        $previousState = $vacationRequest->state;
        $vacationRequest->state->transitionTo($state);
        $vacationRequest->save();

        $this->fireStateChangedEvent($vacationRequest, $previousState, $vacationRequest->state, $user);
    }

    protected function fireStateChangedEvent(
        VacationRequest $vacationRequest,
        ?VacationRequestState $from,
        VacationRequestState $to,
        ?User $user = null,
    ): void {
        $event = new VacationRequestStateChanged($vacationRequest, $from, $to, $user);
        $this->dispatcher->dispatch($event);
    }
}
