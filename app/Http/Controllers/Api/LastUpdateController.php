<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class LastUpdateController
{
    public function __invoke(CacheManager $cache): JsonResponse
    {
        return new JsonResponse([
            "lastUpdate" => $cache->get("last:update", Carbon::now()->toIso8601String()),
        ]);
    }
}
