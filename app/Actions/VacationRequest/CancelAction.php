<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Events\VacationRequestChanged;
use Toby\Jobs\ClearVacationRequestDaysInGoogleCalendar;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestStatusChangedNotification;

class CancelAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->cancel($vacationRequest, $user);

        ClearVacationRequestDaysInGoogleCalendar::dispatch($vacationRequest);

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            $this->notify($vacationRequest);
        }

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
