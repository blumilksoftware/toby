<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Browser::macro("fillMonth", function ($month) {
            $this->select("div.flatpickr-calendar > div.flatpickr-months select", $month - 1);
            return $this;
        });

        Browser::macro("fillDay", function ($day) {
            $this->click("div.flatpickr-calendar.animate.arrowTop.arrowLeft.open > div.flatpickr-innerContainer > div > div.flatpickr-days > div > span:nth-child({$day})");
            return $this;
        });
    }
}
