<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

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
            "role" => "Human Resources Manager",
            "avatar" => asset($this->avatar),
            "deleted" => $this->trashed(),
            "employmentForm" => $this->employment_form->label(),
            "employmentDate" => $this->employment_date->toDisplayString(),
        ];
    }
}
