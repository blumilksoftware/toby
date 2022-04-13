<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KeyResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "owner" => new UserResource($this->owner),
            "previousOwner" => new UserResource($this->previousOwner),
            "updatedAt" => $this->updated_at->toDatetimeString(),
        ];
    }
}
