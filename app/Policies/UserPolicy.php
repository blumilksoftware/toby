<?php

declare(strict_types=1);

namespace Toby\Policies;

use Toby\Enums\EmploymentForm;
use Toby\Models\User;

class UserPolicy
{
    public function canUseOvertimeRequestFunctionality(User $user): bool
    {
        return $user->profile->employment_form->value === EmploymentForm::EmploymentContract->value;
    }
}
