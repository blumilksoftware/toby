<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new SimpleUserResource($this->user),
            "name" => $this->name,
            "description" => $this->description,
            "educationCount" => $this->education->count(),
            "languageCount" => $this->languages->count(),
            "technologyCount" => $this->technologies->count(),
            "projectCount" => $this->projects->count(),
            "createdAt" => $this->created_at->toDisplayString(),
            "updatedAt" => $this->updated_at->toDisplayString(),
        ];
    }
}
