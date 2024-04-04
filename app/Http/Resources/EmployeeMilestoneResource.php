<?php

declare(strict_types=1);

namespace Toby\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class EmployeeMilestoneResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $upcomingBirthday = $this->upcomingBirthday();
        $seniority = $this->seniority();
        $isSeniorityAnniversaryToday = $this->isWorkAnniversaryToday();

        return [
            "user" => new SimpleUserResource($this->resource),
            "birthdayDisplayDate" => $upcomingBirthday?->toDisplayString(),
            "birthdayRelativeDate" => $upcomingBirthday?->isToday()
                ? __("today")
                : $upcomingBirthday?->diffForHumans(
                    Carbon::today(),
                    ["options" => CarbonInterface::ONE_DAY_WORDS, "syntax" => CarbonInterface::DIFF_RELATIVE_TO_NOW],
                ),
            "isBirthdayToday" => (bool)$upcomingBirthday?->isToday(),
            "seniorityDisplayDate" => $seniority,
            "isWorkAnniversaryToday" => $isSeniorityAnniversaryToday,
            "employmentDate" => $this->profile->employment_date->toDisplayString(),
        ];
    }
}
