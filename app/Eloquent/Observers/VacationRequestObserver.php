<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Events\Dispatcher;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Events\VacationRequestStateChanged;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestObserver
{
    public function __construct(
        protected Auth $auth,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function creating(VacationRequest $vacationRequest): void
    {
        $year = $vacationRequest->from->year;

        $vacationRequestNumber = $vacationRequest->user->vacationRequests()
            ->whereYear("from", $year)
            ->count() + 1;

        $vacationRequest->name = "{$vacationRequestNumber}/${year}";
    }

    public function saved(VacationRequest $vacationRequest): void
    {
        if ($vacationRequest->isDirty("state")) {
            $previousState = $vacationRequest->getOriginal("state");

            $this->fireStateChangedEvent($vacationRequest, $previousState, $vacationRequest->state);
        }
    }

    protected function fireStateChangedEvent(
        VacationRequest $vacationRequest,
        ?VacationRequestState $from,
        VacationRequestState $to,
    ): void {
        $event = new VacationRequestStateChanged($vacationRequest, $from, $to, $this->getAuthUser());

        $this->dispatcher->dispatch($event);
    }

    protected function getAuthUser(): ?User
    {
        /** @var User $user */
        $user = $this->auth->guard()->user();

        return $user;
    }
}
