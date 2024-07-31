<?php

declare(strict_types=1);

namespace Toby\States\VacationRequest;

class WaitingForAdministrative extends VacationRequestState
{
    public static string $name = "waiting_for_administrative";
}
