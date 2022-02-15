<?php

declare(strict_types=1);

namespace Toby\Domain\Enums;

enum VacationRequestState: string
{
    case Created = "created";
    case Canceled = "canceled";
    case Rejected = "rejected";
    case Approved = "approved";
    case WaitingForTechnical = "waiting_for_technical";
    case WaitingForAdministrative = "waiting_for_administrative";
    case AcceptedByTechnical = "accepted_by_technical";
    case AcceptedByAdministrative = "accepted_by_administrative";

    public function label(): string
    {
        return __($this->value);
    }

    public static function pendingStates(): array
    {
        return [
            self::Created,
            self::WaitingForTechnical,
            self::WaitingForAdministrative,
            self::AcceptedByTechnical,
            self::AcceptedByAdministrative,
        ];
    }

    public static function successStates(): array
    {
        return [self::Approved];
    }

    public static function failedStates(): array
    {
        return [
            self::Rejected,
            self::Canceled,
        ];
    }

    public static function filterByStatus(string $filter): array
    {
        return match ($filter) {
            "pending" => VacationRequestState::pendingStates(),
            "success" => VacationRequestState::successStates(),
            "failed" => VacationRequestState::failedStates(),
            default => VacationRequestState::cases(),
        };
    }
}
