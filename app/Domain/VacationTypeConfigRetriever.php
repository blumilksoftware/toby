<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Contracts\Config\Repository;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\VacationType;

class VacationTypeConfigRetriever
{
    public const KEY_TECHNICAL_APPROVAL = "technical_approval";
    public const KEY_ADMINISTRATIVE_APPROVAL = "administrative_approval";
    public const KEY_BILLABLE = "billable";
    public const KEY_HAS_LIMIT = "has_limit";
    public const KEY_AVAILABLE_FOR = "available_for";
    public const KEY_IS_VACATION = "is_vacation";

    public function __construct(
        protected Repository $config,
    ) {}

    public function needsTechnicalApproval(VacationType $type): bool
    {
        return $this->getConfigFor($type)[static::KEY_TECHNICAL_APPROVAL];
    }

    public function needsAdministrativeApproval(VacationType $type): bool
    {
        return $this->getConfigFor($type)[static::KEY_ADMINISTRATIVE_APPROVAL];
    }

    public function isBillable(VacationType $type): bool
    {
        return $this->getConfigFor($type)[static::KEY_BILLABLE];
    }

    public function hasLimit(VacationType $type): bool
    {
        return $this->getConfigFor($type)[static::KEY_HAS_LIMIT];
    }

    public function isVacation(VacationType $type): bool
    {
        return $this->getConfigFor($type)[static::KEY_IS_VACATION];
    }

    public function isAvailableFor(VacationType $type, EmploymentForm $employmentForm): bool
    {
        return in_array($employmentForm, $this->getConfigFor($type)[static::KEY_AVAILABLE_FOR], true);
    }

    protected function getConfigFor(VacationType $type): array
    {
        return $this->config->get("vacation_types.{$type->value}");
    }
}
