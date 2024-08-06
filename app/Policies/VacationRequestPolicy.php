<?php

declare(strict_types=1);

namespace Toby\Policies;

use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\States\VacationRequest\Created;
use Toby\States\VacationRequest\WaitingForAdministrative;
use Toby\States\VacationRequest\WaitingForTechnical;

class VacationRequestPolicy
{
    public function acceptAsAdminApprover(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover");
    }

    public function acceptAsTechApprover(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function skipFlow(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover");
    }

    public function reject(User $user): bool
    {
        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }

    public function cancel(User $user, VacationRequest $vacationRequest): bool
    {
        if ($vacationRequest->user->is($user) && $vacationRequest->type === VacationType::RemoteWork) {
            return true;
        }

        if ($vacationRequest->user->is($user) && $vacationRequest->state->equals(
            Created::class,
            WaitingForAdministrative::class,
            WaitingForTechnical::class,
        )) {
            return true;
        }

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover")
            || $user->hasPermissionTo("cancelRequestsAsTechnicalApprover");
    }

    public function show(User $user, VacationRequest $vacationRequest): bool
    {
        if ($vacationRequest->user->is($user)) {
            return true;
        }

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover") ||
            $user->hasPermissionTo("manageRequestsAsTechnicalApprover");
    }
}
