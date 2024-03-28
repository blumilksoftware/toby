<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Toby\Models\YearPeriod;

class RebuildDocumentNumberingSystem extends Command
{
    protected $signature = "toby:rebuild-document-numbering-system";
    protected $description = "Rebuilds the document numbering system to {number}/{year}";

    public function handle(): void
    {
        $yearPeriods = YearPeriod::all();

        foreach ($yearPeriods as $yearPeriod) {
            $number = 1;

            $vacationRequests = $yearPeriod
                ->vacationRequests()
                ->oldest()
                ->get();

            foreach ($vacationRequests as $vacationRequest) {
                $vacationRequest->update(["name" => "{$number}/{$yearPeriod->year}"]);

                $number++;
            }
        }
    }
}
