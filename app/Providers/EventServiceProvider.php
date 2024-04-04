<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Toby\Events\VacationRequestChanged;
use Toby\Listeners\UpdateDailySummaries;
use Toby\Listeners\UpdateLastUpdateCache;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VacationRequestChanged::class => [
            UpdateDailySummaries::class,
            UpdateLastUpdateCache::class,
        ],
    ];
}
