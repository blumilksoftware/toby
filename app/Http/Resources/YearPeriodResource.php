<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class YearPeriodResource extends JsonResource
{
    public static $wrap = false;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "year" => $this->year,
        ];
    }
}
