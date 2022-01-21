<?php

declare(strict_types=1);

namespace Toby\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\UserResource;

class HandleInertiaRequests extends Middleware
{
    public function __construct(protected YearPeriodRetriever $yearPeriodRetriever)
    {
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            "auth" => fn() => [
                "user" => $user ? new UserResource($user) : null,
            ],
            "flash" => fn() => [
                "success" => $request->session()->get("success"),
                "error" => $request->session()->get("error"),
            ],
            "years" => fn() => $user ? $this->yearPeriodRetriever->links() : [],
        ]);
    }
}
