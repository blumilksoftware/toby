<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

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
            "name" => $this->profile->full_name,
            "avatar" => $this->profile->getAvatar(),
            "email" => $this->email,
            "role" => $this->role,
            "position" => $this->profile->position,
            "employmentForm" => $this->profile->employment_form,
            "birthday" => $this->profile->birthday->toDateString(),
            "slackId" => $this->profile->slack_id,
        ];
    }
}
