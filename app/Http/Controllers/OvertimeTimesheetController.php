<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\OvertimeTimesheetExport;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Month;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\User;

class OvertimeTimesheetController extends Controller
{
    public function __invoke(
        Month $month,
        YearPeriodRetriever $yearPeriodRetriever,
    ): BinaryFileResponse {
        $this->authorize("manageRequestsAsAdministrativeApprover");

        $yearPeriod = $yearPeriodRetriever->selected();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->whereRelation("profile", "employment_form", EmploymentForm::EmploymentContract)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $filename = "overtime-{$carbonMonth->translatedFormat("F Y")}.xlsx";

        $timesheet = (new OvertimeTimesheetExport())
            ->forMonth($carbonMonth)
            ->forUsers($users);

        return Excel::download($timesheet, $filename);
    }
}
