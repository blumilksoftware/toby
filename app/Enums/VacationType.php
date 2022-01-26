<?php

declare(strict_types=1);

namespace Toby\Enums;

enum VacationType: string
{
    case VACATION = "vacation";
    case VACATION_ON_REQUEST = "vacation_on_request";
    case SPECIAL_VACATION = "special_vacation";
    case CHILDCARE_VACATION = "childcare_vacation";
    case TRAINING_VACATION = "training_vacation";
    case UNPAID_VACATION = "unpaid_vacation";
    case VOLUNTEERING_VACATION = "volunteering_vacation";
    case TIME_IN_LIEU = "time_in_lieu";
    case SICK_VACATION = "sick_vacation";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = collect(VacationType::cases());

        return $cases->map(
            fn(VacationType $enum) => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }
}
