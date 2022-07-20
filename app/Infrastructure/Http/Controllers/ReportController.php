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
use Toby\Eloquent\Models\Report;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\CreateReportRequest;
use Toby\Infrastructure\Http\Resources\BenefitResource;
use Toby\Infrastructure\Http\Resources\ReportResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class ReportController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function show(Report $report): Response
    {
        $this->authorize("manageBenefits");

        $reports = Report::query()
            ->whereKeyNot(1)
            ->get();

        return inertia("Report", [
            "report" => new ReportResource($report),
            "reports" => $reports->map(fn(Report $report): array => [
                "id" => $report->id,
                "name" => $report->name,
            ]),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateReportRequest $request): RedirectResponse
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

        /** @var Report $assignedBenefits */
        $assignedBenefits = Report::query()
            ->whereKey(1)
            ->first();

        /** @var Report $report */
        $report = Report::query()
            ->create([
                "name" => $nameReport,
                "users" => SimpleUserResource::collection($users),
                "benefits" => BenefitResource::collection($benefits),
                "data" => Arr::map($assignedBenefits->data, fn($iem): array => Arr::except($iem, "comment")),
                "committed_at" => Carbon::now()->toDateTimeString(),
            ]);

        return redirect()
            ->route("benefits-report.show", $report->id)
            ->with("success", __("Report has been created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function download(Request $request, Report $report): BinaryFileResponse
    {
        $this->authorize("manageBenefits");

        $filename = Str::slug($report->name) . ".xlsx";

        $userIds = $request->query("users", []);

        $timesheet = new BenefitsReportTimesheet($report, $userIds);

        return Excel::download($timesheet, $filename);
    }
}
