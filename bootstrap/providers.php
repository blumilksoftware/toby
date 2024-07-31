<?php

declare(strict_types=1);

use Spatie\Permission\PermissionServiceProvider;
use Toby\Providers\AppServiceProvider;
use Toby\Providers\AuthServiceProvider;
use Toby\Providers\EventServiceProvider;
use Toby\Providers\ObserverServiceProvider;
use Toby\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    TelescopeServiceProvider::class,
    ObserverServiceProvider::class,
    PermissionServiceProvider::class,
];
