<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Enums\VacationType;
use Toby\Http\Resources\VacationRequestResource;

class VacationRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = $request->user()
            ->vacationRequests()
            ->paginate();

        return inertia("VacationRequest/Index", [
            "requests" => VacationRequestResource::collection($requests),
        ]);
    }

    public function create(): Response
    {
        return inertia("VacationRequest/Create", [
            "vacationTypes" => VacationType::casesToSelect(),
        ]);
    }
}
