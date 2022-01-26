<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationRequestResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new UserResource($this->user),
            "type" => $this->type->label(),
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "commment" => $this->comment,
        ];
    }
}
