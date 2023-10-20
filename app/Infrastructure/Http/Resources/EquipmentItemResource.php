<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentItemResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "idNumber" => $this->id_number,
            "name" => $this->name,
            "isMobile" => $this->is_mobile,
            "assignee" => new SimpleUserResource($this->assignee),
            "labels" => $this->labels,
            "assignedAt" => $this->assigned_at?->toDateString(),
            "displayAssignedAt" => $this->assigned_at?->toDisplayString(),
        ];
    }
}
