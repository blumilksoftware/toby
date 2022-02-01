<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Scopes\SelectedYearPeriodScope;

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
