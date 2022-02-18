<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Toby\Domain\Events\VacationRequestAcceptedByAdministrative;
use Toby\Domain\Events\VacationRequestAcceptedByTechnical;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\Events\VacationRequestCancelled;
use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\Events\VacationRequestRejected;
use Toby\Domain\Events\VacationRequestStateChanged;
use Toby\Domain\Events\VacationRequestWaitedForAdministrative;
use Toby\Domain\Events\VacationRequestWaitedForTechnical;
use Toby\Domain\Listeners\CreateVacationRequestActivity;
use Toby\Domain\Listeners\HandleAcceptedByAdministrativeVacationRequest;
use Toby\Domain\Listeners\HandleAcceptedByTechnicalVacationRequest;
use Toby\Domain\Listeners\HandleApprovedVacationRequest;
use Toby\Domain\Listeners\HandleCancelledVacationRequest;
use Toby\Domain\Listeners\HandleCreatedVacationRequest;
use Toby\Domain\Listeners\SendApprovedVacationRequestNotification;
use Toby\Domain\Listeners\SendCancelledVacationRequestNotification;
use Toby\Domain\Listeners\SendCreatedVacationRequestNotification;
use Toby\Domain\Listeners\SendRejectedVacationRequestNotification;
use Toby\Domain\Listeners\SendWaitedForAdministrativeVacationRequestNotification;
use Toby\Domain\Listeners\SendWaitedForTechnicalVacationRequestNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VacationRequestStateChanged::class => [CreateVacationRequestActivity::class],
        VacationRequestCreated::class => [HandleCreatedVacationRequest::class, SendCreatedVacationRequestNotification::class],
        VacationRequestAcceptedByTechnical::class => [HandleAcceptedByTechnicalVacationRequest::class],
        VacationRequestAcceptedByAdministrative::class => [HandleAcceptedByAdministrativeVacationRequest::class],
        VacationRequestApproved::class => [HandleApprovedVacationRequest::class, SendApprovedVacationRequestNotification::class],
        VacationRequestRejected::class => [SendRejectedVacationRequestNotification::class],
        VacationRequestCancelled::class => [HandleCancelledVacationRequest::class, SendCancelledVacationRequestNotification::class],
        VacationRequestWaitedForTechnical::class => [SendWaitedForTechnicalVacationRequestNotification::class],
        VacationRequestWaitedForAdministrative::class => [SendWaitedForAdministrativeVacationRequestNotification::class],
    ];
}
