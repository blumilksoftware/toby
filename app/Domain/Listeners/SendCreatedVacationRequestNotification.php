<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Notifications\VacationRequestCreatedNotification;
use Toby\Domain\Notifications\VacationRequestCreatedOnEmployeeBehalf;

class SendCreatedVacationRequestNotification
{
    public function __construct(
    ) {}

    public function handle(VacationRequestCreated $event): void
    {
        $vacationRequest = $event->vacationRequest;

        if ($vacationRequest->creator->is($vacationRequest->user)) {
            $vacationRequest->user->notify(new VacationRequestCreatedNotification($vacationRequest));
        } else {
            $vacationRequest->user->notify(new VacationRequestCreatedOnEmployeeBehalf($vacationRequest));
        }
    }
}
