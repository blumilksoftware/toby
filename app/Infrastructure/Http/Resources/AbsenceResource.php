<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AbsenceResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new SimpleUserResource($this->user),
            "date" => $this->date->toDisplayString(),
        ];
    }
}
