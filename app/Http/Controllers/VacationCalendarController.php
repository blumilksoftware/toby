<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\CalendarGenerator;
use Toby\Enums\Month;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\User;

class VacationCalendarController extends Controller
{
    public function index(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        CalendarGenerator $calendarGenerator,
        ?string $month = null,
    ): Response {
        $month = Month::fromNameOrCurrent((string)$month);
        $currentUser = $request->user();
        $withTrashedUsers = $currentUser->hasPermissionTo("showInactiveUsers");

        $yearPeriod = $yearPeriodRetriever->selected();
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
            "current" => Month::current(),
            "selected" => $month->value,
            "users" => SimpleUserResource::collection($users),
            "withBlockedUsers" => $withTrashedUsers,
        ]);
    }
}
