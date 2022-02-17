<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestWaitedForAdministrative;
use Toby\Domain\Notifications\VacationRequestWaitedForAdministrativeNotification;
use Toby\Eloquent\Models\User;

class SendWaitedForAdministrativeVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestWaitedForAdministrative $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestWaitedForAdministrativeNotification($event->vacationRequest, $user));
        }
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->where("role", [Role::AdministrativeApprover])
            ->get();
    }
}
