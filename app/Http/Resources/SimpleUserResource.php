<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->profile->full_name,
            "last_name" => $this->profile->last_name,
            "email" => $this->email,
            "avatar" => $this->profile->getAvatar(),
        ];
    }
}
