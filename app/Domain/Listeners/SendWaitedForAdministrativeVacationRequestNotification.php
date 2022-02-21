<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestWaitsForAdminApproval;
use Toby\Domain\Notifications\VacationRequestWaitsForAdminApprovalNotification;
use Toby\Eloquent\Models\User;

class SendWaitedForAdministrativeVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestWaitsForAdminApproval $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestWaitsForAdminApprovalNotification($event->vacationRequest, $user));
        }
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->where("role", [Role::AdministrativeApprover])
            ->get();
    }
}
