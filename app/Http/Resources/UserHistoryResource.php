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
            "from" => $this->from->format("d.m.Y"),
            "to" => $this->to?->format("d.m.Y"),
            "type" => $this->type->value,
            "typeLabel" => $this->type->label(),
            "employmentFormLabel" => $this->employment_form?->label(),
            "employmentForm" => $this->employment_form?->value,
            "isEmployedAtCurrentCompany" => $this->is_employed_at_current_company,
            "comment" => $this->comment,
            "userId" => $this->user_id,
        ];
    }
}
