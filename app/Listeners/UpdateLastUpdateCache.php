<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Toby\Events\VacationRequestChanged;

class UpdateLastUpdateCache
{
    public function __construct(
        protected CacheManager $cacheManager,
    ) {}

    public function handle(VacationRequestChanged $event): void
    {
        $this->cacheManager->set("last:update", Carbon::now()->toIso8601String());
    }
}
