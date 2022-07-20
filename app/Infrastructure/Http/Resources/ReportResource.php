<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "benefits" => $this->benefits,
            "users" => $this->users,
            "data" => $this->data,
            "committedAt" => $this->commited_at,
        ];
    }
}
