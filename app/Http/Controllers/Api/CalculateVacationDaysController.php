<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Toby\Domain\WorkDaysCalculator;
use Toby\Http\Controllers\Controller;
use Toby\Http\Requests\Api\CalculateVacationDaysRequest;

class CalculateVacationDaysController extends Controller
{
    public function __invoke(CalculateVacationDaysRequest $request, WorkDaysCalculator $calculator): JsonResponse
    {
        $days = $calculator->calculateDays($request->from(), $request->to(), $request->vacationType());

        return new JsonResponse($days->map(fn(Carbon $day): string => $day->toDateString())->all());
    }
}
