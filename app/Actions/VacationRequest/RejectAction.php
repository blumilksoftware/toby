<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\VacationRequestStateManager;
use Toby\Events\VacationRequestChanged;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestStatusChangedNotification;

class RejectAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->reject($vacationRequest, $user);

        $this->notify($vacationRequest);

        event(new VacationRequestChanged($vacationRequest));
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $users = Permission::findByName("receiveVacationRequestStatusChangedNotification")
            ->users()
            ->where("id", "!=", $vacationRequest->user->id)
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $vacationRequest->user));
    }
}
