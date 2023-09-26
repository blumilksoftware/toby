<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Toby\Domain\Policies\KeyPolicy;
use Toby\Domain\Policies\VacationRequestPolicy;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\VacationRequest;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        VacationRequest::class => VacationRequestPolicy::class,
        Key::class => KeyPolicy::class,
    ];
}
