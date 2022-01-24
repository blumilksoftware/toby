<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Http\Requests\VacationLimitRequest;
use Toby\Http\Resources\VacationLimitResource;
use Toby\Models\VacationLimit;

class VacationLimitController extends Controller
{
    public function edit(): Response
    {
        $limits = VacationLimit::query()
            ->with("user")
            ->orderByUserField("last_name")
            ->orderByUserField("first_name")
            ->get();

        return inertia("VacationLimits", [
            "limits" => VacationLimitResource::collection($limits),
        ]);
    }

    public function update(VacationLimitRequest $request): RedirectResponse
    {
        $data = $request->data();

        foreach ($request->vacationLimits() as $limit) {
            $limit->update($data[$limit->id]);
        }

        return redirect()
            ->back()
            ->with("success", __("Vacation limits have been updated"));
    }
}
