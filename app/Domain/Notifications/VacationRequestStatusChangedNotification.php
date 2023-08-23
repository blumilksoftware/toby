<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

class VacationRequestStatusChangedNotification extends VacationRequestNotification
{
    protected function buildDescription(): string
    {
        return __("The request :title from user :requester has been :status.", [
            "title" => $this->vacationRequest->name,
            "requester" => $this->vacationRequest->user->profile->full_name,
            "status" => $this->vacationRequest->state->label(),
        ]);
    }
}
