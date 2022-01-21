<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
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
       foreach ($request->data() as $data) {
           $limit = VacationLimit::query()->find($data["id"]);

           $limit->update(Arr::only($data, ["has_vacation", "days"]));
       }

       return redirect()
           ->back()
           ->with("success", __("Vacation limits have been updated"));
    }
}
