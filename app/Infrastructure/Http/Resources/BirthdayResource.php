<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BirthdayResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $upcomingBirthday = $this->upcomingBirthday();

        return [
            "id" => $this->id,
            "displayDate" => $upcomingBirthday->toDisplayString(),
            "relativeDate" => $upcomingBirthday->isToday()
                ? __("today")
                : $upcomingBirthday->diffForHumans(
                    Carbon::today(),
                    ["options" => CarbonInterface::ONE_DAY_WORDS, "syntax" => CarbonInterface::DIFF_RELATIVE_TO_NOW]
                ),
            "user" => new SimpleUserResource($this->resource),
        ];
    }
}
