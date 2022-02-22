<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Notifications\VacationRequestCreatedNotification;

class SendCreatedVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestCreated $event): void
    {
        $event->vacationRequest->user->notify(new VacationRequestCreatedNotification($event->vacationRequest));
    }
}
