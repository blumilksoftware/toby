<?php

declare(strict_types=1);

namespace Toby\Enums;

use Illuminate\Support\Collection;

enum VacationType: string
{
    case RemoteWork = "remote_work";
    case Vacation = "vacation";
    case Sick = "sick_vacation";
    case Special = "special_vacation";
    case Delegation = "delegation";
    case TimeInLieu = "time_in_lieu";
    case Unpaid = "unpaid_vacation";
    case OnRequest = "vacation_on_request";
    case Training = "training_vacation";
    case Childcare = "childcare_vacation";
    case Volunteering = "volunteering_vacation";
    case BloodDonation = "blood_donation_vacation";
    case Absence = "absence";

    public static function casesToSelect(): array
    {
        $cases = VacationType::all();

        return $cases->map(
            fn(VacationType $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }

    public static function all(): Collection
    {
        return new Collection(VacationType::cases());
    }

    public function label(): string
    {
        return __($this->value);
    }
}
