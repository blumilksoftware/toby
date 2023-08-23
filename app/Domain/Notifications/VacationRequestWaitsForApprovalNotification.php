<?php

declare(strict_types=1);

namespace Toby\Domain\Notifications;

use Toby\Domain\States\VacationRequest\WaitingForTechnical;

class VacationRequestWaitsForApprovalNotification extends VacationRequestNotification
{
    protected function buildDescription(): string
    {
        $title = $this->vacationRequest->name;
        $requester = $this->vacationRequest->user->profile->full_name;

        if ($this->vacationRequest->state->equals(WaitingForTechnical::class)) {
            return __("The request :title from user :requester is waiting for your technical approval.", [
                "title" => $title,
                "requester" => $requester,
            ]);
        }

        return __("The request :title from user :requester is waiting for your administrative approval.", [
            "title" => $title,
            "requester" => $requester,
        ]);
    }
}
