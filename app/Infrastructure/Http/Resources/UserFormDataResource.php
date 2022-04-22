<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFormDataResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "firstName" => $this->profile->first_name,
            "lastName" => $this->profile->last_name,
            "email" => $this->email,
            "role" => $this->role,
            "position" => $this->profile->position,
            "employmentForm" => $this->profile->employment_form,
            "employmentDate" => $this->profile->employment_date->toDateString(),
            "birthday" => $this->profile->birthday->toDateString(),
            "slackId" => $this->profile->slack_id,
        ];
    }
}
