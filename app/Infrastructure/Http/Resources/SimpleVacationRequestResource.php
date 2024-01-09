<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Toby\Domain\VacationRequestStatesRetriever;

class SimpleVacationRequestResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "type" => $this->type,
            "state" => $this->state,
            "pending" => $this->state->equals(...VacationRequestStatesRetriever::pendingStates()),
            "from" => $this->from->toDisplayString(),
            "to" => $this->to->toDisplayString(),
            "days" => $this->vacations->count(),
        ];
    }
}
