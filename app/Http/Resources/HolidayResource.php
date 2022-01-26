<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

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
            "displayDate" => $this->date->toDisplayString(),
            "dayOfWeek" => $this->date->dayName,
        ];
    }
}
