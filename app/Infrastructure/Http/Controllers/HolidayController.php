<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Policies\HolidayPolicy;
use Toby\Eloquent\Models\Holiday;
use Toby\Infrastructure\Http\Requests\HolidayRequest;
use Toby\Infrastructure\Http\Resources\HolidayFormDataResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;

class HolidayController extends Controller
{
    public function index(Request $request): Response
    {
        $holidays = Holiday::query()
            ->orderBy("date")
            ->get();

        $user = $request->user();
        return inertia("Holidays/Index", [
            "holidays" => HolidayResource::collection($holidays),
            "can" => [
                "create" => $user->can("create", Holiday::class)
            ],
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
            ->with("success", __("Holiday has been created."));
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
            ->with("success", __("Holiday has been updated."));
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday has been deleted."));
    }
}
