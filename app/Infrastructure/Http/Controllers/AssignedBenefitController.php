<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Response;
use Toby\Domain\Enums\Month;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\AssignedBenefitsRequest;
use Toby\Infrastructure\Http\Resources\BenefitResource;
use Toby\Infrastructure\Http\Resources\BenefitsReportResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class AssignedBenefitController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize("manageBenefits");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $benefits = Benefit::query()
            ->orderBy("name")
            ->get();

        $benefitsReports = BenefitsReport::query()
            ->orderBy("committed_at", "desc")
            ->whereKeyNot(1)
            ->get();

        $assignedBenefits = BenefitsReport::query()
            ->whereKey(1)
            ->first();

        return inertia("BenefitsReport/AssignedBenefits", [
            "current" => Month::current(),
            "users" => SimpleUserResource::collection($users),
            "benefits" => BenefitResource::collection($benefits),
            "benefitsReports" => $benefitsReports->map(fn(BenefitsReport $benefitsReport): array => [
                "id" => $benefitsReport->id,
                "name" => $benefitsReport->name,
            ]),
            "assignedBenefits" => new BenefitsReportResource($assignedBenefits),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(AssignedBenefitsRequest $request): RedirectResponse
    {
        $this->authorize("manageBenefits");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $benefits = Benefit::query()
            ->orderBy("name")
            ->get();

        /** @var BenefitsReport $assignedBenefits */
        $assignedBenefits = BenefitsReport::query()
            ->whereKey(1)
            ->first();

        $data = Arr::where(
            $request->get("data"),
            fn(array $item): bool => $users->contains(fn(User $user): bool => $user->id === $item["user"]),
        );

        $data = Arr::map($data, fn(array $item): array => [
            "user" => $item["user"],
            "comment" => $item["comment"] ?? "",
            "benefits" => Arr::where(
                $item["benefits"],
                fn(array $assignedBenefit): bool => $benefits->contains(
                    fn(Benefit $benefit): bool => $benefit->id === $assignedBenefit["id"],
                ),
            ),
        ]);

        $assignedBenefits->update(["data" => $data]);

        return back()
            ->with("success", __("Assigned benefits updated."));
    }
}
