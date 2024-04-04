<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "user" => [
                "id" => $this->id,
                "name" => $this->profile->full_name,
                "email" => $this->email,
                "avatar" => $this->profile->getAvatar(),
            ],
        ];
    }
}
