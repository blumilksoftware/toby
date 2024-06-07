<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Toby\Models\Key;
use Toby\Models\OvertimeRequest;
use Toby\Models\VacationRequest;
use Toby\Policies\KeyPolicy;
use Toby\Policies\OvertimeRequestPolicy;
use Toby\Policies\VacationRequestPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        OvertimeRequest::class => OvertimeRequestPolicy::class,
        VacationRequest::class => VacationRequestPolicy::class,
        Key::class => KeyPolicy::class,
    ];
}
