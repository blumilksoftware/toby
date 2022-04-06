<?php

declare(strict_types=1);

namespace Toby\Domain\Enums;

use Illuminate\Support\Collection;

enum VacationType: string
{
    case Vacation = "vacation";
    case OnRequest = "vacation_on_request";
    case Special = "special_vacation";
    case Childcare = "childcare_vacation";
    case Training = "training_vacation";
    case Unpaid = "unpaid_vacation";
    case Volunteering = "volunteering_vacation";
    case TimeInLieu = "time_in_lieu";
    case Sick = "sick_vacation";
    case Absence = "absence";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = VacationType::all();

        return $cases->map(
            fn(VacationType $enum) => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }

    public static function all(): Collection
    {
        return new Collection(VacationType::cases());
    }
}
