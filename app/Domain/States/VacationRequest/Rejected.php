<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

class Rejected extends VacationRequestState
{
    public static string $name = "rejected";
}
