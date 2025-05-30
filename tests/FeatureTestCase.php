<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Toby\Models\BenefitsReport;

abstract class FeatureTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Carbon::setTestNow(Carbon::createFromDate(2022, 1, 1));

        BenefitsReport::factory()->create([
            "name" => "current",
            "users" => null,
            "benefits" => null,
            "data" => null,
            "committed_at" => null,
        ]);

        $this->seed(PermissionsSeeder::class);
    }
}
