<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationLimitResource extends JsonResource
{
    public static $wrap = false;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->user),
            "hasVacation" => $this->has_vacation,
            "days" => $this->days,
        ];
    }
}
