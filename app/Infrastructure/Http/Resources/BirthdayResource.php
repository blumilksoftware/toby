<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $upcomingBirthday = $this->upcomingBirthday();

        return [
            "id" => $this->id,
            "displayDate" => $upcomingBirthday->toDisplayString(),
            "relativeDate" => $upcomingBirthday->diffForHumans(),
            "user" => new SimpleUserResource($this->resource),
        ];
    }
}
