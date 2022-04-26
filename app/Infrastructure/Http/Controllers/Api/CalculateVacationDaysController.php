<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Toby\Domain\VacationDaysCalculator;
use Toby\Infrastructure\Http\Controllers\Controller;
use Toby\Infrastructure\Http\Requests\Api\CalculateVacationDaysRequest;

class CalculateVacationDaysController extends Controller
{
    public function __invoke(CalculateVacationDaysRequest $request, VacationDaysCalculator $calculator): JsonResponse
    {
        $days = $calculator->calculateDays($request->from(), $request->to());

        return new JsonResponse($days->map(fn(Carbon $day): string => $day->toDateString())->all());
    }
}
