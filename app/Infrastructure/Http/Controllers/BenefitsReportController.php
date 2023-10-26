<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\BenefitsReportTimesheet;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\BenefitsReportRequest;
use Toby\Infrastructure\Http\Resources\BenefitResource;
use Toby\Infrastructure\Http\Resources\BenefitsReportResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class BenefitsReportController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize("manageBenefits");

        $searchText = $request->query("search");

        $benefitsReports = BenefitsReport::query()
            ->search($searchText)
            ->orderBy("committed_at", "desc")
            ->paginate()
            ->withQueryString();

        return inertia("BenefitsReport/Index", [
            "benefitsReports" => BenefitsReportResource::collection($benefitsReports),
            "filters" => [
                "search" => $searchText,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(BenefitsReport $benefitsReport): Response
    {
        $this->authorize("manageBenefits");

        return inertia("BenefitsReport/Show", [
            "benefitsReport" => new BenefitsReportResource($benefitsReport),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(BenefitsReportRequest $request): RedirectResponse
    {
        $this->authorize("manageBenefits");

        $nameReport = $request->get("name");

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

        $data = $users->map(fn(User $user): array => [
            "user" => $user->id,
            "benefits" => $benefits->map(function (Benefit $benefit) use ($assignedBenefits, $user): array {
                $userBenefits = Arr::first(
                    $assignedBenefits->data ?? [],
                    fn($item): bool => $item["user"] === $user->id,
                );

                $assignedBenefit = Arr::first(
                    $userBenefits["benefits"] ?? [],
                    fn($item): bool => $item["id"] === $benefit->id,
                );

                return [
                    "id" => $benefit->id,
                    "employee" => $assignedBenefit["employee"] ?? null,
                    "employer" => $assignedBenefit["employer"] ?? null,
                ];
            })->toArray(),
        ])->toArray();

        /** @var BenefitsReport $benefitsReport */
        $benefitsReport = BenefitsReport::query()
            ->create([
                "name" => $nameReport,
                "users" => SimpleUserResource::collection($users),
                "benefits" => BenefitResource::collection($benefits),
                "data" => $data,
                "committed_at" => Carbon::now()->toDateTimeString(),
            ]);

        return redirect()
            ->route("benefits-reports.show", $benefitsReport->id)
            ->with(
                "success",
                __("Benefits report :name created.", [
                    "name" => $benefitsReport->name,
                ]),
            );
    }

    /**
     * @throws AuthorizationException
     */
    public function download(Request $request, BenefitsReport $benefitsReport): BinaryFileResponse
    {
        $this->authorize("manageBenefits");

        $filename = Str::slug($benefitsReport->name) . ".xlsx";

        $userIds = $request->query("users", []);

        $timesheet = new BenefitsReportTimesheet($benefitsReport, $userIds);

        return Excel::download($timesheet, $filename);
    }

    public function destroy(BenefitsReport $benefitsReport): RedirectResponse
    {
        $this->authorize("manageBenefits");

        if ($benefitsReport->id === 1) {
            return redirect()
                ->back()
                ->with("error", __("Nie możesz usunąć bazowego raportu"));
        }

        $benefitsReport->delete();

        return redirect()
            ->back()
            ->with("success", __("Benefits report :name deleted.", [
                "name" => $benefitsReport->name,
            ]));
    }
}
