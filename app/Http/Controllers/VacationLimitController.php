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
        return inertia("VacationLimits", [
            "limits" => VacationLimitResource::collection(VacationLimit::query()->with("user")->get()),
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
