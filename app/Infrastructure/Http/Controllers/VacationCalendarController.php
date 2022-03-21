<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\CalendarGenerator;
use Toby\Domain\Enums\Month;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\UserResource;

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

        $yearPeriod = $yearPeriodRetriever->selected();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->where("id", "!=", $currentUser->id)
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $users->prepend($currentUser);

        $calendar = $calendarGenerator->generate($carbonMonth);

        return inertia("Calendar", [
            "calendar" => $calendar,
            "current" => Month::current(),
            "selected" => $month->value,
            "users" => UserResource::collection($users),
            "can" => [
                "generateTimesheet" => $request->user()->can("generateTimesheet"),
            ],
        ]);
    }
}
