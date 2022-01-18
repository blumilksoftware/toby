<?php

declare(strict_types=1);

namespace Toby\Enums;

enum EmploymentForm: string
{
    case EMPLOYMENT_CONTRACT = "employment_contract";
    case COMMISSION_CONTRACT = "commission_contract";
    case B2B_CONTRACT = "b2b_contract";
    case BOARD_MEMBER_CONTRACT = "board_member_contract";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = collect(EmploymentForm::cases());

        return $cases->map(
            fn(EmploymentForm $enum) => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }
}
