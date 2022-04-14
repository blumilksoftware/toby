<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Controllers\Controller;
use Toby\Infrastructure\Http\Requests\Api\GetAvailableVacationTypesRequest;

class GetAvailableVacationTypesController extends Controller
{
    public function __invoke(
        GetAvailableVacationTypesRequest $request,
        VacationTypeConfigRetriever $configRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));

        $types = VacationType::all()
            ->filter(fn(VacationType $type) => $configRetriever->isAvailableFor($type, $user->profile->employment_form))
            ->map(fn(VacationType $type) => [
                "label" => $type->label(),
                "value" => $type->value,
            ])
            ->values();

        return new JsonResponse($types);
    }
}
