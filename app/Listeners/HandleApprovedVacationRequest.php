<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Illuminate\Support\Facades\Log;
use Toby\Events\VacationRequestApproved;
use Toby\Helpers\VacationTypeConfigRetriever;

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
