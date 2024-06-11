<?php

declare(strict_types=1);

namespace Toby\Enums;

enum SettlementType: string
{
    case Hours = "hours";
    case Money = "money";

    public function label(): string
    {
        return __($this->value);
    }

    public static function casesToSelect(): array
    {
        return array_map(
            fn(SettlementType $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
            SettlementType::cases(),
        )
    }
}
