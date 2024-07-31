<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonPeriod;
use Generator;
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
use Toby\Helpers\DateFormats;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\States\OvertimeRequest\Approved;

class OvertimeTimesheetPerUserSheet implements WithTitle, WithHeadings, WithEvents, WithStyles, WithStrictNullComparison, ShouldAutoSize, FromGenerator
{
    use RegistersEventListeners;

    public function __construct(
        protected User $user,
        protected Carbon $month,
    ) {}

    public function title(): string
    {
        return $this->user->profile->full_name;
    }

    public function headings(): array
    {
        return [
            __("Date"),
            __("Day of week"),
            __("Start date"),
            __("End date"),
            __("Overtime hours"),
            __("Overtime settlement"),
        ];
    }

    public function generator(): Generator
    {
        $period = CarbonPeriod::create($this->month->copy()->startOfMonth(), $this->month->copy()->endOfMonth());
        $overtimeRequests = $this->getOvertimeForPeriod($this->user, $period);

        foreach ($period as $day) {
            $overtimeRequestsForDay = $overtimeRequests->get($day->toDateString(), new Collection());

            if (!$overtimeRequestsForDay->isEmpty()) {
                /** @var OvertimeRequest $overtimeForDay */
                foreach ($overtimeRequestsForDay as $overtimeForDay) {
                    $row = [
                        Date::dateTimeToExcel($day),
                        $day->translatedFormat("l"),
                        $overtimeForDay->from->format(DateFormats::DATETIME),
                        $overtimeForDay->to->format(DateFormats::DATETIME),
                        $overtimeForDay->hours,
                        __($overtimeForDay->settlement_type->value),
                    ];

                    yield $row;
                }
            } else {
                $row = [
                    Date::dateTimeToExcel($day),
                    $day->translatedFormat("l"),
                ];

                yield $row;
            }
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

    protected function getOvertimeForPeriod(User $user, CarbonPeriod $period): Collection
    {
        return $user->overtimeRequests()
            ->whereBetween("from", [$period->start, $period->end])
            ->states([Approved::$name])
            ->get()
            ->groupBy(fn(OvertimeRequest $overtime): string => $overtime->from->toDateString());
    }
}
