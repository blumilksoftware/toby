<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Toby\Models\BenefitsReport;

class CreateFirstBenefitsReport extends Command
{
    protected $signature = "toby:create-first-benefits-report";
    protected $description = "Create first report for benefits.";

    public function handle(): void
    {
        BenefitsReport::query()
            ->create([
                "name" => "current",
                "users" => null,
                "benefits" => null,
                "data" => null,
                "committed_at" => null,
            ]);
    }
}
