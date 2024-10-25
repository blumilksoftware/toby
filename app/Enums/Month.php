<?php

declare(strict_types=1);

namespace Toby\Enums;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

enum Month: string
{
    case January = "january";
    case February = "february";
    case March = "march";
    case April = "april";
    case May = "may";
    case June = "june";
    case July = "july";
    case August = "august";
    case September = "september";
    case October = "october";
    case November = "november";
    case December = "december";

    public static function current(): Month
    {
        return Month::from(Str::lower(Carbon::now()->englishMonth));
    }

    public static function fromNameOrCurrent(string $name): Month
    {
        return Month::tryFrom($name) ?? Month::current();
    }

    public function toCarbonNumber(): int
    {
        return match ($this) {
            self::January => CarbonInterface::JANUARY,
            self::February => CarbonInterface::FEBRUARY,
            self::March => CarbonInterface::MARCH,
            self::April => CarbonInterface::APRIL,
            self::May => CarbonInterface::MAY,
            self::June => CarbonInterface::JUNE,
            self::July => CarbonInterface::JULY,
            self::August => CarbonInterface::AUGUST,
            self::September => CarbonInterface::SEPTEMBER,
            self::October => CarbonInterface::OCTOBER,
            self::November => CarbonInterface::NOVEMBER,
            self::December => CarbonInterface::DECEMBER,
        };
    }
}
