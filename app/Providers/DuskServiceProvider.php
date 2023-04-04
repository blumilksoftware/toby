<?php

namespace Toby\Providers;

use Laravel\Dusk\Browser;
use Illuminate\Support\ServiceProvider;
use Facebook\WebDriver\WebDriverBy;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
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
        
        Browser::macro("fillGrid", function ($data,$value) {
            $this->type("tr.group:nth-child(1) > td:nth-child({$data}) > input:nth-child(1)",$value);
            return $this;
        });
    }
}