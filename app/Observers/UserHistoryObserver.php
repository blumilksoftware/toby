<?php

declare(strict_types=1);

namespace Toby\Observers;

use Laragear\CacheQuery\Facades\CacheQuery;
use Toby\Models\UserHistory;

class UserHistoryObserver
{
    public function creating(UserHistory $userHistory): void
    {
        CacheQuery::forget("userHistories{$userHistory->user->id}");
    }

    public function updating(UserHistory $userHistory): void
    {
        CacheQuery::forget("userHistories{$userHistory->user->id}");
    }
}
