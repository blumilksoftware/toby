<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserBenefitsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this["id"],
            "name" => $this["name"],
            "employee" => $this["employee"],
            "employer" => $this["employer"],
        ];
    }
}
