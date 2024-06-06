<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Contracts\Events\Dispatcher;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\States\OvertimeRequest\AcceptedByTechnical;
use Toby\States\OvertimeRequest\Approved;
use Toby\States\OvertimeRequest\Cancelled;
use Toby\States\OvertimeRequest\OvertimeRequestState;
use Toby\States\OvertimeRequest\Rejected;
use Toby\States\OvertimeRequest\WaitingForTechnical;

class OvertimeRequestStateManager
{
    public function __construct(
        protected Dispatcher $dispatcher,
    ) {}

    public function markAsCreated(OvertimeRequest $overtimeRequest): void
    {
        $this->createActivity($overtimeRequest, null, $overtimeRequest->state, $overtimeRequest->creator);
    }

    public function approve(OvertimeRequest $overtimeRequest, ?User $user = null): void
    {
        $this->changeState($overtimeRequest, Approved::class, $user);
    }

    public function reject(OvertimeRequest $overtimeRequest, User $user): void
    {
        $this->changeState($overtimeRequest, Rejected::class, $user);
    }

    public function cancel(OvertimeRequest $overtimeRequest, User $user): void
    {
        $this->changeState($overtimeRequest, Cancelled::class, $user);
    }

    public function acceptAsTechnical(OvertimeRequest $overtimeRequest, User $user): void
    {
        $this->changeState($overtimeRequest, AcceptedByTechnical::class, $user);
    }

    public function waitForTechnical(OvertimeRequest $overtimeRequest): void
    {
        $this->changeState($overtimeRequest, WaitingForTechnical::class);
    }

    protected function changeState(OvertimeRequest $overtimeRequest, string $state, ?User $user = null): void
    {
        $previousState = $overtimeRequest->state;
        $overtimeRequest->state->transitionTo($state);
        $overtimeRequest->save();

        $this->createActivity($overtimeRequest, $previousState, $overtimeRequest->state, $user);
    }

    protected function createActivity(
        OvertimeRequest $overtimeRequest,
        ?OvertimeRequestState $from,
        OvertimeRequestState $to,
        ?User $user = null,
    ): void {
        $overtimeRequest->activities()->create([
            "from" => $from,
            "to" => $to,
            "user_id" => $user?->id,
        ]);
    }
}
