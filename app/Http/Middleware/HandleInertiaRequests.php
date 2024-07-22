<?php

declare(strict_types=1);

namespace Toby\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\UserResource;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\VacationRequest;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
        protected CacheManager $cache,
    ) {}

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            "auth" => $this->getAuthData($request),
            "flash" => $this->getFlashData($request),
            "years" => $this->getYearsData($request),
            "vacationRequestsCount" => $this->getVacationRequestsCount($request),
            "overtimeRequestsCount" => $this->getOvertimeRequestsCount($request),
            "deployInformation" => $this->getDeployInformation(),
            "lastUpdate" => $this->cache->rememberForever("last:update", fn(): string => Carbon::now()->toIso8601String()),
        ]);
    }

    protected function getAuthData(Request $request): Closure
    {
        /** @var ?User $user */
        $user = $request->user()?->load("profile");

        return fn(): array => [
            "user" => $user ? new UserResource($user) : null,
            "can" => Permission::query()->with("roles")->get()
                ->mapWithKeys(
                    fn(Permission $permission): array => [
                        $permission->name => $user && $user->hasPermissionTo($permission),
                    ],
                ),
            "overtimeEnabled" => $user && $user->can("canUseOvertimeRequestFunctionality", $user),
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

        return fn(): ?int => $user && $user->can("listAllRequests")
            ? VacationRequest::query()
                ->whereBelongsTo($this->yearPeriodRetriever->selected())
                ->states(
                    VacationRequestStatesRetriever::waitingForUserActionStates($user),
                )
                ->count()
            : null;
    }

    protected function getOvertimeRequestsCount(Request $request): Closure
    {
        $user = $request->user();

        return fn(): ?int => $user && $user->can("listAllRequests")
            ? OvertimeRequest::query()
                ->whereBelongsTo($this->yearPeriodRetriever->selected())
                ->states(
                    OvertimeRequestStatesRetriever::waitingForUserActionStates($user),
                )
                ->count()
            : null;
    }

    protected function getDeployInformation(): Closure
    {
        return fn(): array => [
            "version" => config("deploy.version"),
            "date" => config("deploy.date"),
        ];
    }
}
