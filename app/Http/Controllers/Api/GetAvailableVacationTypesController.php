<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Enums\VacationType;
use Toby\Http\Controllers\Controller;
use Toby\Http\Requests\Api\GetAvailableVacationTypesRequest;
use Toby\Models\User;

class GetAvailableVacationTypesController extends Controller
{
    public function __invoke(
        GetAvailableVacationTypesRequest $request,
        VacationTypeConfigRetriever $configRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $currentUser = $request->user();

        $types = VacationType::all()
            ->filter(function (VacationType $type) use ($configRetriever, $user, $currentUser): bool {
                if ($currentUser->can("createRequestsOnBehalfOfEmployee")) {
                    return $configRetriever->isAvailableFor($type, $user->profile->employment_form);
                }

                return $configRetriever->isRequestAllowedFor($type, $currentUser->role)
                    && $configRetriever->isAvailableFor($type, $user->profile->employment_form);
            })
            ->map(fn(VacationType $type): array => [
                "label" => $type->label(),
                "value" => $type->value,
            ])
            ->values();

        return new JsonResponse($types);
    }
}
