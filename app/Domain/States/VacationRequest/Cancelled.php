<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

class Cancelled extends VacationRequestState
{
    public static string $name = "cancelled";
}
