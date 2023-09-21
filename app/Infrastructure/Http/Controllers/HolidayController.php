<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Infrastructure\Http\Requests\HolidayRequest;
use Toby\Infrastructure\Http\Resources\HolidayFormDataResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;

class HolidayController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $yearPeriod = $yearPeriodRetriever->selected();

        $holidays = $yearPeriod
            ->holidays()
            ->orderBy("date")
            ->get();

        return inertia("Holidays/Index", [
            "holidays" => HolidayResource::collection($holidays),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize("manage holidays");

        return inertia("Holidays/Create");
    }

    /**
     * @throws AuthorizationException
     */
    public function store(HolidayRequest $request): RedirectResponse
    {
        $this->authorize("manage holidays");

        Holiday::query()->create($request->data());

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Holiday $holiday): Response
    {
        $this->authorize("manage holidays");

        return inertia("Holidays/Edit", [
            "holiday" => new HolidayFormDataResource($holiday),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(HolidayRequest $request, Holiday $holiday): RedirectResponse
    {
        $this->authorize("manage holidays");

        $holiday->update($request->data());

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday updated."));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Holiday $holiday): RedirectResponse
    {
        $this->authorize("manage holidays");

        $holiday->delete();

        return redirect()
            ->route("holidays.index")
            ->with("success", __("Holiday deleted."));
    }
}
