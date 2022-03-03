<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\Notifications\VacationRequestApprovedNotification;
use Toby\Eloquent\Models\User;

class SendApprovedVacationRequestNotification
{
    public function __construct(
    ) {}

    public function handle(VacationRequestApproved $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestApprovedNotification($event->vacationRequest, $user));
        }

        $event->vacationRequest->user->notify(new VacationRequestApprovedNotification($event->vacationRequest, $event->vacationRequest->user));
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover])
            ->get();
    }
}
