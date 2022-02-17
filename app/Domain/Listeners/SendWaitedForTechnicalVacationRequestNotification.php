<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestWaitedForTechnical;
use Toby\Domain\Notifications\VacationRequestWaitedForTechnicalNotification;
use Toby\Eloquent\Models\User;

class SendWaitedForTechnicalVacationRequestNotification
{
    public function __construct(
    ) {
    }

    public function handle(VacationRequestWaitedForTechnical $event): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestWaitedForTechnicalNotification($event->vacationRequest, $user));
        }
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->where("role", [Role::TechnicalApprover])
            ->get();
    }
}
