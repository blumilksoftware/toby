<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VacationRequestResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "user" => new UserResource($this->user),
            "type" => $this->type->label(),
            "state" => $this->state->label(),
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "comment" => $this->comment,
            "days" => VacationResource::collection($this->vacations),
        ];
    }
}
