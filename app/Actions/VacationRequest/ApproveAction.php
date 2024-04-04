<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Events\VacationRequestChanged;
use Toby\Jobs\SendVacationRequestDaysToGoogleCalendar;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestStatusChangedNotification;

class ApproveAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function execute(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->stateManager->approve($vacationRequest, $user);

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            SendVacationRequestDaysToGoogleCalendar::dispatch($vacationRequest);

            $this->notify($vacationRequest);
        }

        event(new VacationRequestChanged($vacationRequest));
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $users = Permission::findByName("receiveVacationRequestStatusChangedNotification")->users()->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $vacationRequest->user));
    }
}
