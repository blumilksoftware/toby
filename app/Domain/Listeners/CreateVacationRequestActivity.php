<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestStateChanged;

class CreateVacationRequestActivity
{
    public function handle(VacationRequestStateChanged $event): void
    {
        $event->vacationRequest->activities()->create([
            "from" => $event->from,
            "to" => $event->to,
            "user_id" => $event->user?->id,
        ]);
    }
}
