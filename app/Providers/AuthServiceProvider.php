<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Toby\Domain\Policies\KeyPolicy;
use Toby\Domain\Policies\VacationRequestPolicy;
use Toby\Models\Key;
use Toby\Models\VacationRequest;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        VacationRequest::class => VacationRequestPolicy::class,
        Key::class => KeyPolicy::class,
    ];
}
