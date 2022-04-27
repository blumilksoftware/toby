<?php

declare(strict_types=1);

namespace Toby\Domain\Enums;

enum Role: string
{
    case Employee = "employee";
    case Administrator = "administrator";
    case TechnicalApprover = "technical_approver";
    case AdministrativeApprover = "administrative_approver";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = collect(Role::cases());

        return $cases->map(
            fn(Role $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }
}
