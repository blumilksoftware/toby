<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestWaitsForTechApproval;
use Toby\Domain\Notifications\VacationRequestWaitsForTechApprovalNotification;
use Toby\Eloquent\Models\User;

class SendWaitedForTechnicalVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestWaitsForTechApproval $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestWaitsForTechApprovalNotification($event->vacationRequest, $user));
        }
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->where("role", [Role::TechnicalApprover])
            ->get();
    }
}
