<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Toby\Events\VacationRequestAcceptedByAdministrative;
use Toby\Events\VacationRequestAcceptedByTechnical;
use Toby\Events\VacationRequestApproved;
use Toby\Events\VacationRequestCreated;
use Toby\Events\VacationRequestStateChanged;
use Toby\Listeners\CreateVacationRequestActivity;
use Toby\Listeners\HandleAcceptedByAdministrativeVacationRequest;
use Toby\Listeners\HandleAcceptedByTechnicalVacationRequest;
use Toby\Listeners\HandleApprovedVacationRequest;
use Toby\Listeners\HandleCreatedVacationRequest;

class EventServiceProvider extends ServiceProvider
{
    /**
     * TODO: docelowo może osobne klasy + cast z eloquenta, ale to jak zadziała opcja z enumami
     * TODO: dopisać brakujące walidatory
     * TODO: przyciski pokolorować
     * TODO: warunkowo wyświetlać opcje (policy?)
     * TODO: tworzenie wniosku po walidacji
     * TODO: obliczanie dni urlopu
     */

    protected $listen = [
        VacationRequestStateChanged::class => [CreateVacationRequestActivity::class],
        VacationRequestCreated::class => [HandleCreatedVacationRequest::class],
        VacationRequestAcceptedByTechnical::class => [HandleAcceptedByTechnicalVacationRequest::class],
        VacationRequestAcceptedByAdministrative::class => [HandleAcceptedByAdministrativeVacationRequest::class],
        VacationRequestApproved::class => [HandleApprovedVacationRequest::class],
    ];
}
