<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Eloquent\Models\Report;

class CreateFirstReport extends Command
{
    protected $signature = "toby:create-first-report";
    protected $description = "Create first report for benefits.";

    public function handle(): void
    {
        Report::query()
            ->create([
                "name" => "current",
                "users" => null,
                "benefits" => null,
                "data" => null,
                "committed_at" => null,
            ]);
    }
}
