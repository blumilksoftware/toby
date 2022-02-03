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
            "who" => $this->user ? $this->user->fullName : __("System"),
            "to" => $this->to->label(),
        ];
    }
}
