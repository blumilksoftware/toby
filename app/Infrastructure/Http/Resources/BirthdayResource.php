<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "displayDate" => $this->nextBirthday()->toDisplayString(),
            "user" => new SimpleUserResource($this->resource),
        ];
    }
}
