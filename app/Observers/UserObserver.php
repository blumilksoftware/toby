<?php

declare(strict_types=1);

namespace Toby\Observers;

use Illuminate\Support\Facades\Storage;
use Toby\Helpers\UserAvatarGenerator;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\User;

class UserObserver
{
    public function __construct(
        protected UserAvatarGenerator $generator,
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {
    }

    public function created(User $user): void
    {
        $user->saveAvatar($this->generator->generateFor($user));

        $user->vacationLimits()->create([
            "year_period_id" => $this->yearPeriodRetriever->current()->id,
        ]);
    }

    public function updating(User $user): void
    {
        if ($user->isDirty(["first_name", "last_name"])) {
            Storage::delete($user->avatar);
            $user->avatar = $this->generator->generateFor($user);
        }
    }

    public function forceDeleted(User $user): void
    {
        Storage::delete($user->avatar);
    }
}
