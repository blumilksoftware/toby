<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\TimesheetExport;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Month;
use Toby\Enums\VacationType;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\User;

class TimesheetController extends Controller
{
    public function __invoke(
        Month $month,
        YearPeriodRetriever $yearPeriodRetriever,
        VacationTypeConfigRetriever $configRetriever,
    ): BinaryFileResponse {
        $this->authorize("manageRequestsAsAdministrativeApprover");

        $yearPeriod = $yearPeriodRetriever->selected();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->whereRelation("profile", "employment_form", EmploymentForm::EmploymentContract)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $types = VacationType::all()
            ->filter(
                fn(VacationType $type): bool => $configRetriever->isAvailableFor(
                    $type,
                    EmploymentForm::EmploymentContract,
                ) && $configRetriever->isVacation($type),
            );

        $filename = "{$carbonMonth->translatedFormat("F Y")}.xlsx";

        $timesheet = (new TimesheetExport())
            ->forMonth($carbonMonth)
            ->forUsers($users)
            ->forVacationTypes($types);

        return Excel::download($timesheet, $filename);
    }
}
