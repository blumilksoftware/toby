<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->profile->full_name,
            "email" => $this->email,
            "role" => $this->role->label(),
            "position" => $this->profile->position,
            "avatar" => $this->profile->getAvatar(),
            "deleted" => $this->trashed(),
            "lastActiveAt" => $this->last_active_at?->toDateTimeString(),
            "employmentForm" => $this->profile->employment_form->label(),
            "employmentDate" => $this->profile->employment_date->toDisplayString(),
            "lastMedicalExamDate" => $this->profile->last_medical_exam_date?->toDisplayString(),
            "nextMedicalExamDate" => $this->profile->next_medical_exam_date?->toDisplayString(),
            "lastOhsTrainingDate" => $this->profile->last_ohs_training_date?->toDisplayString(),
            "nextOhsTrainingDate" => $this->profile->next_ohs_training_date?->toDisplayString(),
        ];
    }
}
