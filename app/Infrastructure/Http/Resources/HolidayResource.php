<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HolidayResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "date" => $this->date->toDateString(),
            "isPast" => $this->date->endOfDay()->isPast(),
            "displayDate" => $this->date->toDisplayString(),
            "dayOfWeek" => $this->date->dayName,
            "daysToHoliday" => $this->date->diffForHumans([
                "parts" => 2,
                "join" => " i ",
            ]),
        ];
    }
}
