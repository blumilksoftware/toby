<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Browser::macro("fillYear", function ($year) {
            $this->type("div.flatpickr-calendar.animate.arrowTop.arrowLeft.open > div.flatpickr-months input", $year);
            return $this;
        });

        Browser::macro("fillMonth", function ($month) {
            $this->select("div.flatpickr-calendar.animate.arrowTop.arrowLeft.open > div.flatpickr-months select", $month - 1);
            return $this;
        });

        Browser::macro("fillDay", function ($day) {
            $this->click("div.flatpickr-calendar.animate.arrowTop.arrowLeft.open > div.flatpickr-innerContainer > div > div.flatpickr-days > div > span:nth-child({$day})");
            return $this;
        });

        Browser::macro("fillGrid", function ($data, $value) {
            $this->type("tr.group:nth-child(1) > td:nth-child({$data}) > input:nth-child(1)", $value);
            return $this;
        });

        Browser::macro("dropDownButton", function () {
            $this->click(".rotate-90");
            return $this;
        });

        Browser::macro("changeMonthInResume", function ($month) {
            $this->click(".open > .flatpickr-innerContainer > .flatpickr-rContainer > .flatpickr-monthSelect-months > span:nth-child($month)");
            return $this;
        });

        Browser::macro("enterValue", function ($value, $number, $name) {
            $this->click("#{$value}-{$number}-level")
                ->keys("#{$value}-{$number}-level", $name, "{enter}");
            return $this;
        });

        Browser::macro("enterProjectTechnology", function ($number, $name) {
            $this->click("#project-technologies-{$number}")
                ->keys("#project-technologies-{$number}", $name, "{enter}");
            return $this;
        });
    }
}
