<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "role" => "Human Resources Manager",
            "avatar" => asset($this->avatar),
            "employmentForm" => $this->employment_form->label(),
            "employmentStartDate" => $this->employment_start_date->translatedFormat("j F Y"),
        ];
    }
}
