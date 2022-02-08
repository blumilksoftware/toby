<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;
use Toby\Infrastructure\Http\Resources\UserResource;

class VacationCalendarController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $month = $request->query("month", "february");
        $yearPeriod = $yearPeriodRetriever->selected();
        $date = CarbonImmutable::create($yearPeriod->year, $this->monthNameToNumber($month));
        $period = CarbonPeriod::create($date->startOfMonth(), $date->endOfMonth());
        $holidays = $yearPeriod->holidays()->pluck("date");
        $users = User::query()
            ->with([
                "vacations" => fn($query) => $query
                    ->whereBetween("date", [$period->start, $period->end])
                    ->whereRelation("vacationRequest", "state", VacationRequestState::APPROVED->value)
            ])
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $calendar = [];

        foreach ($period as $day) {
            $calendar[] = [
                "date" => $day->toDateString(),
                "dayOfMonth" => $day->translatedFormat("j"),
                "dayOfWeek" => $day->translatedFormat("D"),
                "isToday" => $day->isToday(),
                "isWeekend" => $day->isWeekend(),
                "isHoliday" => $holidays->contains($day),
            ];
        }

        $userVacations = [];

        /** @var User $user */
        foreach ($users as $user) {
            $userVacations[] = [
                "user" => new UserResource($user),
                "vacations" => $user->vacations->map(fn (Vacation $vacation) => $vacation->date->toDateString()),
            ];
        }

        return inertia("Calendar", [
            "calendar" => $calendar,
            "currentMonth" => $month,
            "userVacations" => $userVacations,
        ]);
    }

    protected function monthNameToNumber(?string $name): int
    {
        return match($name) {
            default => CarbonInterface::JANUARY,
            "february" => CarbonInterface::FEBRUARY,
            "march" => CarbonInterface::MARCH,
            "april" => CarbonInterface::APRIL,
            "may" => CarbonInterface::MAY,
            "june" => CarbonInterface::JUNE,
            "julu" => CarbonInterface::JULY,
            "august" => CarbonInterface::AUGUST,
            "septemter" => CarbonInterface::SEPTEMBER,
            "october" => CarbonInterface::OCTOBER,
            "november" => CarbonInterface::NOVEMBER,
            "december" => CarbonInterface::DECEMBER,
        };
    }
}
