<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\CalendarGenerator;
use Toby\Enums\Month;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class VacationCalendarController extends Controller
{
    public function index(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        CalendarGenerator $calendarGenerator,
        ?string $month = null,
        ?int $year = null,
    ): Response|RedirectResponse {
        if ($year !== null) {
            return $this->changeYearPeriod($request, $month, $year);
        }

        $month = Month::fromNameOrCurrent((string)$month);
        $currentUser = $request->user();
        $withTrashedUsers = $currentUser->canSeeInactiveUsers();

        $yearPeriod = $yearPeriodRetriever->selected();
        $previousYearPeriod = YearPeriod::query()
            ->where("year", "<", $yearPeriod->year)
            ->orderBy("year", "desc")
            ->first();
        $nextYearPeriod = YearPeriod::query()
            ->where("year", ">", $yearPeriod->year)
            ->orderBy("year")
            ->first();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->withTrashed($withTrashedUsers)
            ->where("id", "!=", $currentUser->id)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $users->prepend($currentUser);

        $calendar = $calendarGenerator->generate($carbonMonth);

        return inertia("Calendar", [
            "calendar" => $calendar,
            "currentMonth" => Month::current(),
            "currentYear" => Carbon::now()->year,
            "selectedMonth" => $month->value,
            "selectedYear" => $yearPeriod->year,
            "users" => SimpleUserResource::collection($users),
            "withBlockedUsers" => $withTrashedUsers,
            "previousYearPeriod" => $previousYearPeriod,
            "nextYearPeriod" => $nextYearPeriod,
        ]);
    }

    private function changeYearPeriod(Request $request, string $month, int $year): RedirectResponse
    {
        $yearPeriod = YearPeriod::query()->where("year", $year)->firstOrFail();
        $request->session()->put(YearPeriodRetriever::SESSION_KEY, $yearPeriod->id);

        return redirect()->route("calendar", ["month" => $month])
            ->with("info", __("Year period changed."));
    }
}
