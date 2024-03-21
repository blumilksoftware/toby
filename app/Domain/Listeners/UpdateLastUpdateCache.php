<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Psr\SimpleCache\InvalidArgumentException;
use Toby\Domain\Events\VacationRequestChanged;

class UpdateLastUpdateCache
{
    public function __construct(
        protected CacheManager $cacheManager,
    ) {}

    public function handle(VacationRequestChanged $event): void
    {
        $this->cacheManager->set("last_update", Carbon::now()->toIso8601String());
    }
}
