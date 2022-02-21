<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

class Approved extends VacationRequestState
{
    public static string $name = "approved";
}
