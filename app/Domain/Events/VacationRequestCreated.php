<?php

declare(strict_types=1);

namespace Toby\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public VacationRequest $vacationRequest,
    ) {}
}
