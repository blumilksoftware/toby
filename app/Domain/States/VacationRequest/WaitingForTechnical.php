<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

class WaitingForTechnical extends VacationRequestState
{
    public static string $name = "waiting_for_technical";
}
