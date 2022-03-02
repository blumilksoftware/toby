<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

class AcceptedByTechnical extends VacationRequestState
{
    public static string $name = "accepted_by_technical";
}
