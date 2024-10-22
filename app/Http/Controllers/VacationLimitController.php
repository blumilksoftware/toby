<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Http\Requests\TakeDaysFromLastYearRequest;
use Toby\Http\Requests\VacationLimitRequest;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;
use Toby\Models\VacationLimit;

class VacationLimitController extends Controller
{
    public function edit(Request $request, UserVacationStatsRetriever $statsRetriever): Response
    {
        $this->authorize("manageVacationLimits");

        $year = $request->integer("year", Carbon::now()->year);

        $users = User::query()
            ->with(["vacationLimits" => fn(HasMany $query): HasMany => $query->where("year", $year)->limit(1)])
            ->withTrashed($request->user()->canSeeInactiveUsers())
            ->get()
            ->sortBy(fn(User $user): string => "{$user->profile->last_name} {$user->profile->first_name}")
            ->values();

        $limitsResource = $users->map(function (User $user) use ($statsRetriever, $year): array {
            $limit = $user->vacationLimits->first();

            return [
                "user" => new SimpleUserResource($user),
                "hasVacation" => $limit?->hasVacation() ?? false,
                "days" => $limit?->days ?? 0,
                "limit" => $limit?->limit ?? 0,
                "fromPreviousYear" => $limit?->from_previous_year ?? 0,
                "toNextYear" => $limit?->to_next_year ?? 0,
                "remainingLastYear" => $statsRetriever->getRemainingVacationDays($user, $year - 1),
            ];
        });

        return inertia("VacationLimits", [
            "year" => $year,
            "limits" => $limitsResource,
        ]);
    }

    public function update(VacationLimitRequest $request): RedirectResponse
    {
        $this->authorize("manageVacationLimits");

        foreach ($request->data() as $limit) {
            VacationLimit::query()->updateOrCreate(
                [
                    "user_id" => $limit["user"],
                    "year" => $limit["year"],
                ],
                ["days" => $limit["days"]],
            );
        }

        return redirect()
            ->back()
            ->with("success", __("Vacation limits updated."));
    }

    public function takeFromLastYear(TakeDaysFromLastYearRequest $request): RedirectResponse
    {
        $this->authorize("manageVacationLimits");

        $data = $request->getData();

        $limit = VacationLimit::query()->updateOrCreate([
            "user_id" => $data["user"],
            "year" => $data["year"],
        ], [
            "from_previous_year" => $data["days"],
        ]);

        $previousLimit = $limit->user->vacationLimits()
            ->where("year", $limit->year - 1)
            ->first();

        $previousLimit?->update([
            "to_next_year" => $data["days"],
        ]);

        return redirect()
            ->back()
            ->with("success", __("Days moved."));
    }
}
