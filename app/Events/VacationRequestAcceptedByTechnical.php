<?php

declare(strict_types=1);

namespace Toby\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Toby\Models\VacationRequest;

class VacationRequestAcceptedByTechnical
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public VacationRequest $vacationRequest,
    ) {
    }
}