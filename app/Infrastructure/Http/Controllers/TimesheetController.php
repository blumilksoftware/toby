<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\TimesheetExport;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;

class TimesheetController extends Controller
{
    public function __invoke(Request $request, YearPeriodRetriever $yearPeriodRetriever): BinaryFileResponse
    {
        $yearPeriod = $yearPeriodRetriever->selected();
        $month = Str::lower($request->query("month", Carbon::now()->englishMonth));

        $month = Carbon::create($yearPeriod->year, $this->monthNameToNumber($month));

        $users = User::query()
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $filename = "{$month->translatedFormat("F Y")}.xlsx";

        $timesheet = (new TimesheetExport())
            ->forMonth($month)
            ->forUsers($users);

        return Excel::download($timesheet, $filename);
    }

    protected function monthNameToNumber(string $name): int
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
