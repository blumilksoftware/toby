<?php

declare(strict_types=1);

namespace Toby\Notifications;

use Illuminate\Bus\Queueable;
use Toby\Models\VacationRequest;

class VacationRequestCreatedNotification extends VacationRequestNotification
{
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
    ) {
        parent::__construct($this->vacationRequest, $this->vacationRequest->user);
    }

    protected function buildDescription(): string
    {
        $name = $this->vacationRequest->name;

        if ($this->vacationRequest->creator()->is($this->vacationRequest->user)) {
            return __("The request :title has been created.", [
                "requester" => $this->vacationRequest->user->profile->full_name,
                "title" => $name,
            ]);
        }

        return __("The request :title has been created by user :creator on your behalf.", [
            "title" => $this->vacationRequest->name,
            "creator" => $this->vacationRequest->creator->profile->full_name,
        ]);
    }
}
