<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Collection;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestNotification;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestNotificationSender
{
    public function sendVacationRequestNotification(VacationRequest $vacationRequest): void
    {
        foreach ($this->getUsersForNotifications() as $user) {
            $user->notify(new VacationRequestNotification($user, $vacationRequest));
        }

        $vacationRequest->user->notify(new VacationRequestNotification($vacationRequest->user, $vacationRequest));
    }

    protected function getUsersForNotifications(): Collection
    {
        return User::query()
            ->where("role", Role::TECHNICAL_APPROVER)
            ->orWhere("role", Role::ADMINISTRATIVE_APPROVER)
            ->get();
    }
}
