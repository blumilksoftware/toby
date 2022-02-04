<?php

declare(strict_types=1);

namespace Toby\Domain\Enums;

enum VacationRequestState: string
{
    case CREATED = "created";
    case CANCELED = "canceled";
    case REJECTED = "rejected";
    case APPROVED = "approved";
    case WAITING_FOR_TECHNICAL = "waiting_for_technical";
    case WAITING_FOR_ADMINISTRATIVE = "waiting_for_administrative";
    case ACCEPTED_BY_TECHNICAL = "accepted_by_technical";
    case ACCEPTED_BY_ADMINISTRATIVE = "accepted_by_administrative";

    public function label(): string
    {
        return __($this->value);
    }

    public static function pendingStates(): array
    {
        return [
            self::CREATED,
            self::WAITING_FOR_TECHNICAL,
            self::WAITING_FOR_ADMINISTRATIVE,
            self::ACCEPTED_BY_TECHNICAL,
            self::ACCEPTED_BY_ADMINISTRATIVE,
        ];
    }

    public static function successStates(): array
    {
        return [self::APPROVED];
    }

    public static function failedStates(): array
    {
        return [
            self::REJECTED,
            self::CANCELED,
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
