<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserHistoryResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "from" => $this->from->format("Y-m-d"),
            "to" => $this->to->toDateString("Y-m-d"),
            "type" => $this->type->value,
            "typeLabel" => $this->type->label(),
            "employmentFormLabel" => $this->employment_form?->label(),
            "employmentForm" => $this->employment_form?->value,
            "comment" => $this->comment,
            "userId" => $this->user_id,
        ];
    }
}
