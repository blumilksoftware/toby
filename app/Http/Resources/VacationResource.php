<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new SimpleUserResource($this->user),
            "displayDate" => $this->date->toDisplayString(),
            "date" => $this->date->toDateString(),
        ];
    }
}
