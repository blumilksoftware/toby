<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;
use Toby\Domain\VacationRequestStatesRetriever;
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
        return array_merge(parent::share($request), [
            "auth" => $this->getAuthData($request),
            "flash" => $this->getFlashData($request),
            "years" => $this->getYearsData($request),
            "vacationRequestsCount" => $this->getVacationRequestsCount($request),
            "deployInformation" => $this->getDeployInformation(),
        ]);
    }

    protected function getAuthData(Request $request): Closure
    {
        $user = $request->user();

        return fn(): array => [
            "user" => $user ? new UserResource($user) : null,
            "can" => [
                "manageVacationLimits" => $user ? $user->can("manageVacationLimits") : false,
                "manageUsers" => $user ? $user->can("manageUsers") : false,
                "listAllVacationRequests" => $user ? $user->can("listAll", VacationRequest::class) : false,
                "listMonthlyUsage" => $user ? $user->can("listMonthlyUsage") : false,
                "manageResumes" => $user ? $user->can("manageResumes") : false,
            ],
        ];
    }

    protected function getFlashData(Request $request): Closure
    {
        return fn(): array => [
            "success" => $request->session()->get("success"),
            "error" => $request->session()->get("error"),
            "info" => $request->session()->get("info"),
        ];
    }

    protected function getYearsData(Request $request): Closure
    {
        return fn(): array => $request->user() ? $this->yearPeriodRetriever->links() : [];
    }

    protected function getVacationRequestsCount(Request $request): Closure
    {
        $user = $request->user();

        return fn(): ?int => $user && $user->can("listAll", VacationRequest::class)
        ? VacationRequest::query()
            ->whereBelongsTo($this->yearPeriodRetriever->selected())
            ->states(
                VacationRequestStatesRetriever::waitingForUserActionStates($user),
            )
            ->count()
        : null;
    }

    protected function getDeployInformation(): Closure
    {
        return fn(): array => [
            "release_version" => Config::get("heroku.release_version"),
            "slug_description" => Config::get("heroku.slug_description"),
            "release_created_at" => Config::get("heroku.release_created_at"),
            "slug_commit" => Config::get("heroku.slug_commit"),
            "github_url" => preg_replace("/\/$/i", "", Config::get("heroku.github_url", "")),
        ];
    }
}
