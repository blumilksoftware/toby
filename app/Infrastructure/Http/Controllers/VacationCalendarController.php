<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Response;
use Toby\Domain\CalendarGenerator;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\UserResource;

class VacationCalendarController extends Controller
{
    public function index(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
        CalendarGenerator $calendarGenerator,
    ): Response {
        $month = Str::lower($request->query("month", Carbon::now()->englishMonth));
        $yearPeriod = $yearPeriodRetriever->selected();
        $users = User::query()
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $calendar = $calendarGenerator->generate($yearPeriod, $month);

        return inertia("Calendar", [
            "calendar" => $calendar,
            "currentMonth" => $month,
            "users" => UserResource::collection($users),
        ]);
    }

    protected function monthNameToNumber(?string $name): int
    {
        return match ($name) {
            default => CarbonInterface::JANUARY,
            "february" => CarbonInterface::FEBRUARY,
            "march" => CarbonInterface::MARCH,
            "april" => CarbonInterface::APRIL,
            "may" => CarbonInterface::MAY,
            "june" => CarbonInterface::JUNE,
            "july" => CarbonInterface::JULY,
            "august" => CarbonInterface::AUGUST,
            "september" => CarbonInterface::SEPTEMBER,
            "october" => CarbonInterface::OCTOBER,
            "november" => CarbonInterface::NOVEMBER,
            "december" => CarbonInterface::DECEMBER,
        };
    }
}
