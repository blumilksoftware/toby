<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Toby\Domain\Events\VacationRequestAcceptedByAdministrative;
use Toby\Domain\Events\VacationRequestAcceptedByTechnical;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\Events\VacationRequestCanceled;
use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Events\VacationRequestStateChanged;
use Toby\Domain\Listeners\CreateVacationRequestActivity;
use Toby\Domain\Listeners\HandleAcceptedByAdministrativeVacationRequest;
use Toby\Domain\Listeners\HandleAcceptedByTechnicalVacationRequest;
use Toby\Domain\Listeners\HandleApprovedVacationRequest;
use Toby\Domain\Listeners\HandleCanceledVacationRequest;
use Toby\Domain\Listeners\HandleCreatedVacationRequest;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VacationRequestStateChanged::class => [CreateVacationRequestActivity::class],
        VacationRequestCreated::class => [HandleCreatedVacationRequest::class],
        VacationRequestAcceptedByTechnical::class => [HandleAcceptedByTechnicalVacationRequest::class],
        VacationRequestAcceptedByAdministrative::class => [HandleAcceptedByAdministrativeVacationRequest::class],
        VacationRequestApproved::class => [HandleApprovedVacationRequest::class],
        VacationRequestCanceled::class => [HandleCanceledVacationRequest::class],
    ];
}
