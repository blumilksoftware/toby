<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Models\Benefit;
use Toby\Infrastructure\Http\Requests\BenefitRequest;
use Toby\Infrastructure\Http\Resources\BenefitResource;

class BenefitController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $benefits = Benefit::query()
            ->orderBy("name")
            ->paginate()
            ->withQueryString();

        return inertia("Benefits/Benefits", [
            "benefits" => BenefitResource::collection($benefits),
            "can" => [
                "manageBenefits" => $request->user()->can("manageBenefits"),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(BenefitRequest $request): RedirectResponse
    {
        $this->authorize("manageBenefits");

        /** @var Benefit $benefit */
        $benefit = Benefit::query()->create($request->only("name", "companion"));

        return back()
            ->with(
                "success",
                __("Benefit :name created.", [
                    "name" => $benefit->name,
                ]),
            );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Benefit $benefit): RedirectResponse
    {
        $this->authorize("manageBenefits");

        $benefit->delete();

        return back()
            ->with(
                "success",
                __("Benefit :name deleted.", [
                    "name" => $benefit->name,
                ]),
            );
    }
}
