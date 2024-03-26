<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KeyResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new SimpleUserResource($this->user),
            "updatedAt" => $this->updated_at->toDisplayString(),
            "can" => [
                "give" => $request->user()->can("give", $this->resource),
                "take" => !$this->user()->is($request->user()),
            ],
        ];
    }
}
