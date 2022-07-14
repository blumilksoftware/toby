<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Tests\Traits\InteractsWithYearPeriods;

abstract class FeatureTestCase extends BaseTestCase
{
    use CreatesApplication;
    use InteractsWithYearPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();

        Carbon::setTestNow(Carbon::createFromDate(2022, 1, 1));
        $this->createCurrentYearPeriod();
    }
}
