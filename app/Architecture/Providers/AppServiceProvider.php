<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::macro("toDisplayString", fn() => $this->translatedFormat("j F Y"));
        Carbon::macro("toDisplayDate", fn() => $this->translatedFormat("d.m.Y"));
    }
}
