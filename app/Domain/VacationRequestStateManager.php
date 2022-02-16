<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Events\Dispatcher;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Events\VacationRequestAcceptedByAdministrative;
use Toby\Domain\Events\VacationRequestAcceptedByTechnical;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Events\VacationRequestRejected;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestStateManager
{
    public function __construct(
        protected Auth $auth,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function markAsCreated(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::Created);

        $this->dispatcher->dispatch(new VacationRequestCreated($vacationRequest));
    }

    public function approve(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::Approved);

        $this->dispatcher->dispatch(new VacationRequestApproved($vacationRequest));
    }

    public function reject(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::Rejected);
        $this->dispatcher->dispatch(new VacationRequestRejected($vacationRequest));
    }

    public function cancel(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::Canceled);
    }

    public function acceptAsTechnical(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::AcceptedByTechnical);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByTechnical($vacationRequest));
    }

    public function acceptAsAdministrative(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::AcceptedByAdministrative);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByAdministrative($vacationRequest));
    }

    public function waitForTechnical(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::WaitingForTechnical);
    }

    public function waitForAdministrative(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::WaitingForAdministrative);
    }

    protected function changeState(VacationRequest $vacationRequest, VacationRequestState $state): void
    {
        $vacationRequest->changeStateTo($state);
    }
}
