<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Response;
use Toby\Domain\Enums\Month;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\Report;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\AssignedBenefitsRequest;
use Toby\Infrastructure\Http\Resources\BenefitResource;
use Toby\Infrastructure\Http\Resources\ReportResource;
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

        $reports = Report::query()
            ->orderBy("committed_at", "desc")
            ->whereKeyNot(1)
            ->get();

        $assignedBenefits = Report::query()
            ->whereKey(1)
            ->first();

        return inertia("Report/AssignedBenefits", [
            "current" => Month::current(),
            "users" => SimpleUserResource::collection($users),
            "benefits" => BenefitResource::collection($benefits),
            "reports" => $reports->map(fn(Report $report): array => [
                "id" => $report->id,
                "name" => $report->name,
            ]),
            "assignedBenefits" => new ReportResource($assignedBenefits),
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

        /** @var Report $assignedBenefits */
        $assignedBenefits = Report::query()
            ->whereKey(1)
            ->first();

        $data = Arr::where(
            $request->get("data"),
            fn(array $item): bool => $users->contains(fn(User $user): bool => $user->id === $item["user"]),
        );

        $data = Arr::map($data, fn(array $item): array => [
            "user" => $item["user"],
            "comment" => $item["comment"],
            "benefits" => Arr::where(
                $item["benefits"],
                fn(array $assignedBenefit): bool => $benefits->contains(
                    fn(Benefit $benefit): bool => $benefit->id === $assignedBenefit["id"],
                ),
            ),
        ]);

        $assignedBenefits->update(["data" => $data]);

        return back()
            ->with("success", __("Assigned benefits has been updated."));
    }
}
