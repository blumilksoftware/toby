<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Toby\Domain\Events\VacationRequestChanged;
use Toby\Domain\Listeners\UpdateDailySummaries;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VacationRequestChanged::class => [
            UpdateDailySummaries::class,
        ],
    ];
}
