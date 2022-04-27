<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;

class TimesheetPerUserSheet implements WithTitle, WithHeadings, WithEvents, WithStyles, WithStrictNullComparison, ShouldAutoSize, FromGenerator
{
    use RegistersEventListeners;

    protected const HOURS_PER_DAY = 8;
    protected const START_HOUR = 8;
    protected const END_HOUR = 16;

    public function __construct(
        protected User $user,
        protected Carbon $month,
        protected Collection $types,
    ) {}

    public function title(): string
    {
        return $this->user->profile->full_name;
    }

    public function headings(): array
    {
        $headings = [
            __("Date"),
            __("Day of week"),
            __("Start date"),
            __("End date"),
            __("Worked hours"),
        ];

        foreach ($this->types as $type) {
            $headings[] = $type->label();
        }

        return $headings;
    }

    public function generator(): Generator
    {
        $period = CarbonPeriod::create($this->month->copy()->startOfMonth(), $this->month->copy()->endOfMonth());
        $vacations = $this->getVacationsForPeriod($this->user, $period);
        $holidays = $this->getHolidaysForPeriod($period);

        foreach ($period as $day) {
            $vacationsForDay = $vacations->get($day->toDateString(), new Collection());
            $workedThisDay = $this->checkIfWorkedThisDay($day, $holidays, $vacationsForDay);

            $row = [
                Date::dateTimeToExcel($day),
                $day->translatedFormat("l"),
                $workedThisDay ? $this->toExcelTime(Carbon::createFromTime(static::START_HOUR)) : null,
                $workedThisDay ? $this->toExcelTime(Carbon::createFromTime(static::END_HOUR)) : null,
                $workedThisDay ? static::HOURS_PER_DAY : null,
            ];

            foreach (VacationType::cases() as $type) {
                $row[] = $vacationsForDay->has($type->value) ? static::HOURS_PER_DAY : null;
            }

            yield $row;
        }
    }

    public function styles(Worksheet $sheet): void
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFont()->setBold(true);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB("D9D9D9");

        $sheet->getStyle("C1:{$lastColumn}{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A2:A{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->getStyle("C1:D{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_TIME3);

        $sheet->getStyle("A2:A{$lastRow}")
            ->getFont()
            ->setBold(true);

        for ($i = 2; $i < $lastRow; $i++) {
            $date = Date::excelToDateTimeObject($sheet->getCell("A{$i}")->getValue());

            if (Carbon::createFromInterface($date)->isWeekend()) {
                $sheet->getStyle("A{$i}:{$lastColumn}{$i}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB("FEE2E2");
            }
        }

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()
            ->setRGB("B7B7B7");
    }

    public static function afterSheet(AfterSheet $event): void
    {
        $sheet = $event->getSheet();
        $lastRow = $sheet->getDelegate()->getHighestRow();

        $sheet->append([
            __("Sum:"),
            null,
            null,
            null,
            "=SUM(E2:E{$lastRow})",
        ]);

        $lastRow++;

        $sheet->getDelegate()->getStyle("A{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getDelegate()->getStyle("A{$lastRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getDelegate()->getStyle("E{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getDelegate()->mergeCells("A{$lastRow}:D{$lastRow}");

        $sheet->getDelegate()->getStyle("A{$lastRow}:E{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()
            ->setRGB("B7B7B7");
    }

    protected function getVacationsForPeriod(User $user, CarbonPeriod $period): Collection
    {
        return $user->vacations()
            ->with("vacationRequest")
            ->whereRelation("vacationRequest", fn(Builder $query): Builder => $query->whereIn("type", $this->types))
            ->whereBetween("date", [$period->start, $period->end])
            ->approved()
            ->get()
            ->groupBy(
                [
                    fn(Vacation $vacation): string => $vacation->date->toDateString(),
                    fn(Vacation $vacation): string => $vacation->vacationRequest->type->value,
                ],
            );
    }

    protected function getHolidaysForPeriod(CarbonPeriod $period): Collection
    {
        return Holiday::query()
            ->whereBetween("date", [$period->start, $period->end])
            ->pluck("date");
    }

    protected function toExcelTime(Carbon $time): float
    {
        $excelTimestamp = Date::dateTimeToExcel($time);
        $excelDate = floor($excelTimestamp);

        return $excelTimestamp - $excelDate;
    }

    protected function checkIfWorkedThisDay(CarbonInterface $day, Collection $holidays, Collection $vacations): bool
    {
        return $day->isWeekday() && $holidays->doesntContain($day) && $vacations->isEmpty();
    }
}
