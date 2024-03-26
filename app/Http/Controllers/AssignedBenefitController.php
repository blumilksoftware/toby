<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Response;
use Toby\Domain\Enums\Month;
use Toby\Http\Requests\AssignedBenefitsRequest;
use Toby\Http\Resources\BenefitResource;
use Toby\Http\Resources\BenefitsReportResource;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\Benefit;
use Toby\Models\BenefitsReport;
use Toby\Models\User;

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

        $assignedBenefits = BenefitsReport::query()
            ->withoutGlobalScope("withoutAssignedBenefitReport")
            ->first();

        return inertia("AssignedBenefits/AssignedBenefits", [
            "current" => Month::current(),
            "users" => SimpleUserResource::collection($users),
            "benefits" => BenefitResource::collection($benefits),
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
            ->withoutGlobalScope("withoutAssignedBenefitReport")
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
