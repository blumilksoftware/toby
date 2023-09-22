<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\Events\VacationRequestChanged;
use Toby\Domain\Notifications\VacationRequestStatusChangedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Jobs\ClearVacationRequestDaysInGoogleCalendar;

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
        $users = Permission::findByName("receiveVacationRequestStatusChangedNotification")->users()->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $vacationRequest->user));
    }
}
