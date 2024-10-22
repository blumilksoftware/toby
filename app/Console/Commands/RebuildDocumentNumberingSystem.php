<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Toby\Models\VacationRequest;

class RebuildDocumentNumberingSystem extends Command
{
    protected $signature = "toby:rebuild-document-numbering-system";
    protected $description = "Rebuilds the document numbering system to {number}/{year}";

    public function handle(): void
    {
        $years = DB::table(VacationRequest::class)
            ->select([DB::raw("YEAR(from) as year")])
            ->groupBy("year")
            ->value("year");

        foreach ($years as $year) {
            $number = 1;

            $vacationRequests = VacationRequest::query()
                ->whereYear("date", $year)
                ->get();

            foreach ($vacationRequests as $vacationRequest) {
                $vacationRequest->update(["name" => "{$number}/{$year}"]);

                $number++;
            }
        }
    }
}
