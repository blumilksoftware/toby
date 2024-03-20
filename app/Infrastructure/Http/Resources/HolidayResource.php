<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $name
 * @property Carbon $date
 */
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
            "isSaturday" => $this->date->endOfDay()->isSaturday(),
            "displayDate" => $this->date->toDisplayString(),
            "dayOfWeek" => $this->date->dayName,
            "daysToHoliday" => (int)$this->date->diffInDays(),
            "displayDaysToHoliday" => $this->date->diffForHumans(),
        ];
    }
}
