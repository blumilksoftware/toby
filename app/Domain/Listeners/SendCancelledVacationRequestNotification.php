<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestCancelled;
use Toby\Domain\Notifications\VacationRequestCancelledNotification;
use Toby\Eloquent\Models\User;

class SendCancelledVacationRequestNotification
{
    public function __construct(
    ) {}

    public function handle(VacationRequestCancelled $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestCancelledNotification($event->vacationRequest, $user));
        }

        $event->vacationRequest->user->notify(new VacationRequestCancelledNotification($event->vacationRequest, $event->vacationRequest->user));
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover])
            ->get();
    }
}
