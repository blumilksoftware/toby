<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeFormResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user" => new SimpleUserResource($this->user),
            "name" => $this->name,
            "description" => $this->description,
            "education" => $this->education,
            "languages" => $this->languages,
            "technologies" => $this->technologies,
            "projects" => $this->projects,
        ];
    }
}
