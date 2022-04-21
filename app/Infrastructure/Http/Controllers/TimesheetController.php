<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Month;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\TimesheetExport;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;

class TimesheetController extends Controller
{
    public function __invoke(
        Month $month,
        YearPeriodRetriever $yearPeriodRetriever,
        VacationTypeConfigRetriever $configRetriever,
    ): BinaryFileResponse {
        $this->authorize("generateTimesheet");

        $yearPeriod = $yearPeriodRetriever->selected();
        $carbonMonth = Carbon::create($yearPeriod->year, $month->toCarbonNumber());

        $users = User::query()
            ->whereRelation("profile", "employment_form", EmploymentForm::EmploymentContract)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        $types = VacationType::all()
            ->filter(
                fn(VacationType $type) => $configRetriever->isAvailableFor($type, EmploymentForm::EmploymentContract)
                    && $configRetriever->isVacation($type),
            );

        $filename = "{$carbonMonth->translatedFormat("F Y")}.xlsx";

        $timesheet = (new TimesheetExport())
            ->forMonth($carbonMonth)
            ->forUsers($users)
            ->forVacationTypes($types);

        return Excel::download($timesheet, $filename);
    }
}
