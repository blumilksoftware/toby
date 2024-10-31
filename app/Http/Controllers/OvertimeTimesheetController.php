<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\OvertimeTimesheetExport;
use Toby\Enums\EmploymentForm;
use Toby\Models\User;

class OvertimeTimesheetController extends Controller
{
    public function __invoke(?string $month = null): BinaryFileResponse
    {
        $this->authorize("manageRequestsAsAdministrativeApprover");

        $month = Carbon::canBeCreatedFromFormat($month, "m-Y")
            ? Carbon::createFromFormat("d-m-Y", "01-$month")
            : Carbon::now();

        $users = User::query()
            ->whereRelation("profile", "employment_form", EmploymentForm::EmploymentContract)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $filename = "overtime-{$month->translatedFormat("F Y")}.xlsx";

        $timesheet = (new OvertimeTimesheetExport())
            ->forMonth($month)
            ->forUsers($users);

        return Excel::download($timesheet, $filename);
    }
}
