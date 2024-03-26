<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Models\VacationRequest;

class VacationTypeCanBeSelected implements VacationRequestRule
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function check(VacationRequest $vacationRequest): bool
    {
        $employmentForm = $vacationRequest->user->profile->employment_form;

        $availableTypes = VacationType::all()
            ->filter(fn(VacationType $type): bool => $this->configRetriever->isAvailableFor($type, $employmentForm));

        return $availableTypes->contains($vacationRequest->type);
    }

    public function errorMessage(): string
    {
        return __("You cannot create vacation request of this type.");
    }
}
