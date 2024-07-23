<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $lastMedicalExam = $this->lastMedicalExam();
        $lastOhsTraining = $this->lastOhsTraining();
        $startOfEmploymentInCurrentCompany = $this->startOfEmploymentInCurrentCompany();

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
            "employmentDate" => $startOfEmploymentInCurrentCompany?->from->toDisplayString(),
            "lastMedicalExamDate" => $lastMedicalExam?->from?->toDisplayString(),
            "nextMedicalExamDate" => $lastMedicalExam?->to?->toDisplayString(),
            "lastOhsTrainingDate" => $lastOhsTraining?->from?->toDisplayString(),
            "nextOhsTrainingDate" => $lastOhsTraining?->to?->toDisplayString(),
        ];
    }
}
