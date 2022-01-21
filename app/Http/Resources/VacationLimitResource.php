<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationLimitResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "user" => new UserResource($this->user),
            "hasVacation" => $this->has_vacation,
            "days" => $this->days,
        ];
    }
}
