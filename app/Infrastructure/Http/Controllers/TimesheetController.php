<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\Enums\Month;
use Toby\Domain\TimesheetExport;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;

class TimesheetController extends Controller
{
    public function __invoke(Month $month, YearPeriodRetriever $yearPeriodRetriever): BinaryFileResponse
    {
        $this->authorize("generateTimesheet");

        $yearPeriod = $yearPeriodRetriever->selected();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $filename = "{$carbonMonth->translatedFormat("F Y")}.xlsx";

        $timesheet = (new TimesheetExport())
            ->forMonth($carbonMonth)
            ->forUsers($users);

        return Excel::download($timesheet, $filename);
    }
}
