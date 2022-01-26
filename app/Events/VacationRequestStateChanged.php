<?php

declare(strict_types=1);

namespace Toby\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Toby\Enums\VacationRequestState;
use Toby\Models\User;
use Toby\Models\VacationRequest;

class VacationRequestStateChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public VacationRequest $vacationRequest,
        public ?VacationRequestState $from,
        public VacationRequestState $to,
        public ?User $user = null,
    ) {
    }
}
