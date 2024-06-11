<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class OvertimeCalculator
{
    public function calculateHours(CarbonInterface $from, CarbonInterface $to): int
    {
        $hours = Carbon::create($from)->diffInMinutes($to);

        return (int)ceil($hours / 60);
    }
}
