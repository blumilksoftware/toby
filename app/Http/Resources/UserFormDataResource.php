<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFormDataResource extends JsonResource
{
    public static $wrap = false;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "employmentForm" => $this->employment_form,
            "employmentDate" => $this->employment_date,
        ];
    }
}