<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Toby\Models\Benefit;
use Toby\Models\BenefitsReport;
use Toby\Models\User;

class UserBenefitsRetriever
{
    public function getAssignedBenefits(User $user): array
    {
        /* @var BenefitsReport $assignedBenefits */
        $assignedBenefits = BenefitsReport::query()
            ->withoutGlobalScope("withoutAssignedBenefitReport")
            ->first();

        $allBenefits = Benefit::query()->get();

        $userAssignedBenefits = $this->findUserData($assignedBenefits, $user);

        if (empty($userAssignedBenefits)) {
            return [];
        }

        $benefits = [];

        foreach ($userAssignedBenefits["benefits"] as $assignedBenefit) {
            if ($this->userDoesntUseBenefit($assignedBenefit) || !$this->benefitExist($allBenefits, $assignedBenefit["id"])) {
                continue;
            }

            $benefit = $allBenefits->first(fn($benefit): bool => $benefit->id === $assignedBenefit["id"]);

            $benefits[] = [
                "id" => $benefit["id"],
                "name" => $benefit["name"],
                "employee" => $assignedBenefit["employee"],
                "employer" => $assignedBenefit["employer"],
            ];
        }

        return $benefits;
    }

    protected function findUserData(BenefitsReport $assignedBenefits, User $user): ?array
    {
        return Arr::first($assignedBenefits->data ?? [], fn($item): bool => $item["user"] === $user->id);
    }

    protected function userDoesntUseBenefit(array $benefit): bool
    {
        return empty($benefit["employee"]) && empty($benefit["employer"]);
    }

    protected function benefitExist(Collection $benefits, int $benefitId): bool
    {
        return $benefits->contains(fn(Benefit $benefit): bool => $benefit->id === $benefitId);
    }
}
