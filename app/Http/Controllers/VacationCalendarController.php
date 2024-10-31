<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\CalendarGenerator;
use Toby\Domain\MonthWorkingHoursCalculator;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;

class VacationCalendarController extends Controller
{
    public function index(
        Request $request,
        CalendarGenerator $calendarGenerator,
        MonthWorkingHoursCalculator $monthWorkingHoursCalculator,
        ?string $month = null,
    ): Response|RedirectResponse {
        $month = Carbon::canBeCreatedFromFormat($month, "m-Y")
            ? Carbon::createFromFormat("d-m-Y", "01-$month")
            : Carbon::now();

        /** @var User $currentUser */
        $currentUser = $request->user();
        $withTrashedUsers = $currentUser->canSeeInactiveUsers();

        $users = User::query()
            ->withTrashed($withTrashedUsers)
            ->where("id", "!=", $currentUser->id)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $users->prepend($currentUser);

        $calendar = $calendarGenerator->generate($month);
        $workingHours = $currentUser->isEmployedOnEmploymentForm()
            ? $monthWorkingHoursCalculator->calculateHours($calendar, $currentUser) * config("toby.number_of_hours_on_employment_contract.full_time")
            : null;

        return inertia("Calendar", [
            "calendar" => $calendar,
            "workingHours" => $workingHours,
            "selectedDate" => $month->toDateString(),
            "users" => SimpleUserResource::collection($users),
            "withBlockedUsers" => $withTrashedUsers,
        ]);
    }
}
