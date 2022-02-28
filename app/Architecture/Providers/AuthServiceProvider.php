<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Toby\Domain\Policies\HolidayPolicy;
use Toby\Eloquent\Models\Holiday;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Holiday::class => HolidayPolicy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
