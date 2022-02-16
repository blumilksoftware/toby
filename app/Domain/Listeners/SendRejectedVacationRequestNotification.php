<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Events\VacationRequestRejected;
use Toby\Domain\Notifications\VacationRequestRejectedNotification;
use Toby\Eloquent\Models\User;

class SendRejectedVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestRejected $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestRejectedNotification($event->vacationRequest, $user));
        }

        $event->vacationRequest->user->notify(new VacationRequestRejectedNotification($event->vacationRequest, $event->vacationRequest->user));
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover])
            ->get();
    }
}
