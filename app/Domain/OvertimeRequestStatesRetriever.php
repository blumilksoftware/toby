<?php

declare(strict_types=1);

namespace Toby\Domain;

use Toby\Enums\Role;
use Toby\Models\User;
use Toby\States\OvertimeRequest\AcceptedByTechnical;
use Toby\States\OvertimeRequest\Approved;
use Toby\States\OvertimeRequest\Cancelled;
use Toby\States\OvertimeRequest\Created;
use Toby\States\OvertimeRequest\Rejected;
use Toby\States\OvertimeRequest\Settled;
use Toby\States\OvertimeRequest\WaitingForTechnical;

class OvertimeRequestStatesRetriever
{
    public static function pendingStates(): array
    {
        return [
            Created::class,
            WaitingForTechnical::class,
            AcceptedByTechnical::class,
        ];
    }

    public static function successStates(): array
    {
        return [
            Approved::class,
            Settled::class,
        ];
    }

    public static function failedStates(): array
    {
        return [
            Rejected::class,
            Cancelled::class,
        ];
    }

    public static function notFailedStates(): array
    {
        return array_merge(static::successStates(), static::pendingStates());
    }

    public static function waitingForUserActionStates(User $user): array
    {
        return match ($user->role) {
            Role::TechnicalApprover, Role::Administrator => [WaitingForTechnical::class],
            default => [],
        };
    }

    public static function all(): array
    {
        return [
            ...self::pendingStates(),
            ...self::successStates(),
            ...self::failedStates(),
        ];
    }

    public static function filterByStatusGroup(string $filter, ?User $user = null): array
    {
        return match ($filter) {
            "pending" => self::pendingStates(),
            "success" => self::successStates(),
            "failed" => self::failedStates(),
            "waiting_for_action" => self::waitingForUserActionStates($user),
            default => self::all(),
        };
    }
}
