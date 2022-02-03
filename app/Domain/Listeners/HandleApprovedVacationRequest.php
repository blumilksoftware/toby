<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Illuminate\Support\Facades\Log;
use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\VacationTypeConfigRetriever;

class HandleApprovedVacationRequest
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {
    }

    public function handle(VacationRequestApproved $event): void
    {
        $vacationRequest = $event->vacationRequest;

        Log::info("approved! {$vacationRequest->id}");
    }
}
