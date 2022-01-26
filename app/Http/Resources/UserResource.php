<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->fullName,
            "email" => $this->email,
            "role" => $this->role->label(),
            "avatar" => asset($this->avatar),
            "deleted" => $this->trashed(),
            "employmentForm" => $this->employment_form->label(),
            "employmentDate" => $this->employment_date->toDisplayString(),
        ];
    }
}
