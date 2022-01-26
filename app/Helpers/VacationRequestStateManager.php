<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Events\Dispatcher;
use Toby\Enums\VacationRequestState;
use Toby\Events\VacationRequestAcceptedByAdministrative;
use Toby\Events\VacationRequestAcceptedByTechnical;
use Toby\Events\VacationRequestApproved;
use Toby\Events\VacationRequestCreated;
use Toby\Models\VacationRequest;

class VacationRequestStateManager
{
    public function __construct(
        protected Auth $auth,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function markAsCreated(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::CREATED);

        $this->dispatcher->dispatch(new VacationRequestCreated($vacationRequest));
    }

    public function approve(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::APPROVED);

        $this->dispatcher->dispatch(new VacationRequestApproved($vacationRequest));
    }

    public function reject(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::REJECTED);
    }

    public function cancel(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::CANCELED);
    }

    public function acceptAsTechnical(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::ACCEPTED_BY_TECHNICAL);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByTechnical($vacationRequest));
    }

    public function acceptAsAdministrative(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::ACCEPTED_BY_ADMINSTRATIVE);

        $this->dispatcher->dispatch(new VacationRequestAcceptedByAdministrative($vacationRequest));
    }

    public function waitForTechnical(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::WAITING_FOR_TECHNICAL);
    }

    public function waitForAdministrative(VacationRequest $vacationRequest): void
    {
        $this->changeState($vacationRequest, VacationRequestState::WAITING_FOR_ADMINISTRATIVE);
    }

    protected function changeState(VacationRequest $vacationRequest, VacationRequestState $state): void
    {
        $vacationRequest->changeStateTo($state);
    }
}
