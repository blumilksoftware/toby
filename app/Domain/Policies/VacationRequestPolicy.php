<?php

declare(strict_types=1);

namespace Toby\Domain\Policies;

use Toby\Domain\Enums\VacationType;
use Toby\Domain\States\VacationRequest\Created;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

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

        return $user->hasPermissionTo("manageRequestsAsAdministrativeApprover");
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
