<?php

declare(strict_types=1);

namespace Toby\Policies;

use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\States\OvertimeRequest\Approved;
use Toby\States\OvertimeRequest\Created;
use Toby\States\OvertimeRequest\WaitingForTechnical;

class OvertimeRequestPolicy
{
    public function acceptAsTechApprover(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function reject(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function cancel(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($overtimeRequest->user->is($user) && $overtimeRequest->state->equals(
            Created::class,
            WaitingForTechnical::class,
        )) {
            return true;
        }

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function settle(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($overtimeRequest->user->is($user) && $overtimeRequest->state->equals(
            Approved::class,
        )) {
            return true;
        }

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function show(User $user, OvertimeRequest $overtimeRequest): bool
    {
        if ($overtimeRequest->user->is($user)) {
            return true;
        }

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }
}
