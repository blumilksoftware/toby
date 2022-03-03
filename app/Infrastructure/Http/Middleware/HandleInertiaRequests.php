<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Resources\UserResource;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {}

    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            "auth" => fn() => [
                "user" => $user ? new UserResource($user) : null,
                "can" => [
                    "manageVacationLimits" => $user ? $user->can("manageVacationLimits") : false,
                    "manageUsers" => $user ? $user->can("manageUsers") : false,
                    "listAllVacationRequests" => $user ? $user->can("listAll", VacationRequest::class) : false,
                ],
            ],
            "flash" => fn() => [
                "success" => $request->session()->get("success"),
                "error" => $request->session()->get("error"),
            ],
            "years" => fn() => $user ? $this->yearPeriodRetriever->links() : [],
        ]);
    }
}
