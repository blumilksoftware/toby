<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Toby\Domain\Enums\Role;
use Toby\Domain\Policies\KeyPolicy;
use Toby\Domain\Policies\VacationRequestPolicy;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        VacationRequest::class => VacationRequestPolicy::class,
        Key::class => KeyPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user) {
            if ($user->role === Role::Administrator) {
                return true;
            }
        });

        Gate::define("manageUsers", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
        Gate::define("manageHolidays", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
        Gate::define("manageVacationLimits", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
        Gate::define("generateTimesheet", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
        Gate::define("listMonthlyUsage", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
        Gate::define("manageResumes", fn(User $user): bool => $user->role === Role::AdministrativeApprover);
    }
}
