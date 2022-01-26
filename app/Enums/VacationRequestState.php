<?php

declare(strict_types=1);

namespace Toby\Enums;

enum VacationRequestState: string
{
    case CREATED = "created";
    case CANCELED = "canceled";
    case REJECTED = "rejected";
    case APPROVED = "approved";
    case WAITING_FOR_TECHNICAL = "waiting_for_technical";
    case WAITING_FOR_ADMINISTRATIVE = "waiting_for_administrative";
    case ACCEPTED_BY_TECHNICAL = "accepted_by_technical";
    case ACCEPTED_BY_ADMINSTRATIVE = "accepted_by_administrative";

    public function label(): string
    {
        return __($this->value);
    }
}
