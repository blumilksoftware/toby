<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Notifications\VacationRequestCreatedNotification;
use Toby\Domain\Notifications\VacationRequestCreatedOnEmployeeBehalf;

class SendCreatedVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestCreated $event): void
    {
        $vacationRequest = $event->vacationRequest;

        if ($vacationRequest->creator->is($vacationRequest->user)) {
            $event->vacationRequest->user->notify(new VacationRequestCreatedNotification($event->vacationRequest));
        } else {
            $event->vacationRequest->user->notify(new VacationRequestCreatedOnEmployeeBehalf($event->vacationRequest));
        }
    }
}
