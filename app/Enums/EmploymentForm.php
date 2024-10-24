<?php

declare(strict_types=1);

namespace Toby\Enums;

enum EmploymentForm: string
{
    case EmploymentContract = "employment_contract";
    case CommissionContract = "commission_contract";
    case B2bContract = "b2b_contract";
    case BoardMemberContract = "board_member_contract";

    public static function casesToSelect(): array
    {
        $cases = collect(EmploymentForm::cases());

        return $cases->map(
            fn(EmploymentForm $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }

    public function label(): string
    {
        return __($this->value);
    }
}
