<?php

declare(strict_types=1);

namespace Toby\Domain\Enums;

enum Role: string
{
    case EMPLOYEE = "employee";
    case ADMINISTRATOR = "administrator";
    case TECHNICAL_APPROVER = "technical_approver";
    case ADMINISTRATIVE_APPROVER = "administrative_approver";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = collect(Role::cases());

        return $cases->map(
            fn(Role $enum) => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }
}
