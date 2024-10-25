<?php

declare(strict_types=1);

namespace Toby\Domain;

use Generator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements WithHeadings, WithStyles, WithEvents, WithStrictNullComparison, WithColumnFormatting, ShouldAutoSize, FromGenerator
{
    use RegistersEventListeners;

    public function __construct(
        protected Collection $equipmentItems,
    ) {}

    public function generator(): Generator
    {
        foreach ($this->equipmentItems as $equipmentItem) {
            $row = [
                $equipmentItem->id_number,
                $equipmentItem->name,
                $equipmentItem->labels?->implode(", "),
                $equipmentItem->is_mobile ? "Tak" : "Nie",
                $equipmentItem->assignee->profile->full_name ?? "",
                $equipmentItem->assigned_at ? Date::dateTimeToExcel($equipmentItem->assigned_at) : "",
            ];

            yield $row;
        }
    }

    public function headings(): array
    {
        return [
            __("ID"),
            __("Name"),
            __("Labels"),
            __("Is mobile"),
            __("Assignee"),
            __("Assigned at"),
        ];
    }

    public function columnFormats(): array
    {
        return [
            "A" => NumberFormat::FORMAT_TEXT,
            "B" => NumberFormat::FORMAT_TEXT,
            "C" => NumberFormat::FORMAT_TEXT,
            "D" => DataType::TYPE_BOOL,
            "E" => NumberFormat::FORMAT_TEXT,
            "F" => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB("D9D9D9");

        $sheet->getStyle("A2:A{$lastRow}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()
            ->setRGB("B7B7B7");
    }
}
