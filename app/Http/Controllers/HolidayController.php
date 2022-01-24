<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Http\Requests\HolidayRequest;
use Toby\Http\Resources\HolidayFormDataResource;
use Toby\Http\Resources\HolidayResource;
use Toby\Models\Holiday;

class HolidayController extends Controller
{
    public function index(): Response
    {
        $holidays = Holiday::query()
            ->orderBy("date")
            ->get();

        return inertia("Holidays/Index", [
            "holidays" => HolidayResource::collection($holidays),
        ]);
    }

    public function create(): Response
    {
        return inertia("Holidays/Create");
    }

    public function store(HolidayRequest $request): RedirectResponse
    {
        Holiday::query()->create($request->data());

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday has been created"));
    }

    public function edit(Holiday $holiday): Response
    {
        return inertia("Holidays/Edit", [
            "holiday" => new HolidayFormDataResource($holiday),
        ]);
    }

    public function update(HolidayRequest $request, Holiday $holiday): RedirectResponse
    {
        $holiday->update($request->data());

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday has been updated"));
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday has been deleted"));
    }
}
