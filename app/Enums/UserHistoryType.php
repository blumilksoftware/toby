<?php

declare(strict_types=1);

namespace Toby\Enums;

enum UserHistoryType: string
{
    case Employment = "employment";
    case MedicalExam = "medical_exam";
    case OhsTraining = "ohs_training";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        $cases = collect(UserHistoryType::cases());

        return $cases->map(
            fn(UserHistoryType $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }
}
