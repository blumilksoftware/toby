<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BenefitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->resource["id"] ?? $this->id,
            "name" => $this->resource["name"] ?? $this->name,
            "companion" => $this->resource["companion"] ?? $this->companion,
            "isUsed" => $this->resource["isUsed"] ?? false,
        ];
    }
}
