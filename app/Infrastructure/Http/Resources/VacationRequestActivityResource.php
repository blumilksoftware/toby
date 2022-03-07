<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationRequestActivityResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "date" => $this->created_at->toDisplayString(),
            "time" => $this->created_at->format("H:i"),
            "user" => $this->user ? $this->user->fullName : __("System"),
            "state" => $this->to,
        ];
    }
}
