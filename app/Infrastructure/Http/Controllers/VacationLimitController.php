<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Infrastructure\Http\Requests\VacationLimitRequest;
use Toby\Infrastructure\Http\Resources\VacationLimitResource;

class VacationLimitController extends Controller
{
    public function edit(): Response
    {
        $this->authorize("manageVacationLimits");

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
        $this->authorize("manageVacationLimits");

        $data = $request->data();

        foreach ($request->vacationLimits() as $limit) {
            $limit->update($data[$limit->id]);
        }

        return redirect()
            ->back()
            ->with("success", __("Vacation limits have been updated."));
    }
}
