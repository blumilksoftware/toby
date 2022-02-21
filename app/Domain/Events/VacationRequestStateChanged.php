<?php

declare(strict_types=1);

namespace Toby\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Toby\Domain\States\VacationRequest\VacationRequestState;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

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
