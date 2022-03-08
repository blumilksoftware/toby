<?php

declare(strict_types=1);

namespace Toby\Domain\Policies;

use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestPolicy
{
    public function listAll(User $user): bool
    {
        return in_array($user->role, [Role::AdministrativeApprover, Role::TechnicalApprover], true);
    }

    public function createOnBehalfOfEmployee(User $user): bool
    {
        return $user->role === Role::AdministrativeApprover;
    }

    public function acceptAsAdminApprover(User $user): bool
    {
        return $user->role === Role::AdministrativeApprover;
    }

    public function acceptAsTechApprover(User $user): bool
    {
        return $user->role === Role::TechnicalApprover;
    }

    public function skipFlow(User $user): bool
    {
        return $user->role === Role::AdministrativeApprover;
    }

    public function reject(User $user): bool
    {
        return in_array($user->role, [Role::AdministrativeApprover, Role::TechnicalApprover], true);
    }

    public function cancel(User $user): bool
    {
        return $user->role === Role::AdministrativeApprover;
    }

    public function show(User $user, VacationRequest $vacationRequest): bool
    {
        if ($vacationRequest->user->is($user)) {
            return true;
        }

        return in_array($user->role, [Role::TechnicalApprover, Role::AdministrativeApprover], true);
    }
}
