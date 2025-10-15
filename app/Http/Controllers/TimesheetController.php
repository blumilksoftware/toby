<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\TimesheetExport;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Enums\EmploymentForm;
use Toby\Enums\VacationType;
use Toby\Models\User;

class TimesheetController extends Controller
{
    public function __invoke(VacationTypeConfigRetriever $configRetriever, ?string $month = null): BinaryFileResponse
    {
        $this->authorize("downloadWorkHoursSummary");

        $month = Carbon::canBeCreatedFromFormat($month, "m-Y")
            ? Carbon::createFromFormat("d-m-Y", "01-$month")
            : Carbon::now();

        $users = User::query()
            ->whereRelation("profile", "employment_form", EmploymentForm::EmploymentContract)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $types = VacationType::all()
            ->filter(fn(VacationType $type): bool => $configRetriever->isVacation($type));

        $filename = "{$month->translatedFormat("F Y")}.xlsx";

        $timesheet = (new TimesheetExport())
            ->forMonth($month)
            ->forUsers($users)
            ->forVacationTypes($types);

        return Excel::download($timesheet, $filename);
    }
}
