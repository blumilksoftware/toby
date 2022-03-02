<?php

declare(strict_types=1);

namespace Toby\Domain;

use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Approved;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;

class VacationRequestStatesRetriever
{
    public static function pendingStates(): array
    {
        return [
            Created::class,
            WaitingForTechnical::class,
            WaitingForAdministrative::class,
            AcceptedByTechnical::class,
            AcceptedByAdministrative::class,
        ];
    }

    public static function successStates(): array
    {
        return [Approved::class];
    }

    public static function failedStates(): array
    {
        return [
            Rejected::class,
            Cancelled::class,
        ];
    }

    public static function all(): array
    {
        return [
            ...self::pendingStates(),
            ...self::successStates(),
            ...self::failedStates(),
        ];
    }

    public static function filterByStatus(string $filter): array
    {
        return match ($filter) {
            "pending" => self::pendingStates(),
            "success" => self::successStates(),
            "failed" => self::failedStates(),
            default => self::all(),
        };
    }
}
