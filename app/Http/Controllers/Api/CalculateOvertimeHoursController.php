<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\OvertimeCalculator;
use Toby\Http\Controllers\Controller;
use Toby\Http\Requests\Api\CalculateOvertimeHoursRequest;

class CalculateOvertimeHoursController extends Controller
{
    public function __invoke(CalculateOvertimeHoursRequest $request, OvertimeCalculator $overtimeCalculator): JsonResponse
    {
        $hours = $overtimeCalculator->calculateHours($request->from(), $request->to());

        return new JsonResponse($hours);
    }
}
