<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Toby\Models\Holiday;
use Toby\Models\VacationLimit;
use Toby\Scopes\SelectedYearPeriodScope;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::macro("toDisplayString", fn() => $this->translatedFormat("j F Y"));

        $selectedYearPeriodScope = $this->app->make(SelectedYearPeriodScope::class);

        VacationLimit::addGlobalScope($selectedYearPeriodScope);
        Holiday::addGlobalScope($selectedYearPeriodScope);
    }
}
