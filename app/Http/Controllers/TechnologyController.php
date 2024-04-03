<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Toby\Http\Requests\TechnologyRequest;
use Toby\Http\Resources\TechnologyResource;
use Toby\Models\Technology;

class TechnologyController extends Controller
{
    public function index(): Response
    {
        $this->authorize("manageResumes");

        $technologies = Technology::query()
            ->orderBy("name")
            ->get();

        return inertia("Technologies", [
            "technologies" => TechnologyResource::collection($technologies),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(TechnologyRequest $request): RedirectResponse
    {
        $this->authorize("manageResumes");

        $technology = Technology::query()->create($request->data());

        return redirect()
            ->back()
            ->with("success", __("Technology :name created.", [
                "name" => $technology->name,
            ]));
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        $this->authorize("manageResumes");

        $technology->delete();

        return redirect()
            ->back()
            ->with("success", __("Technology :name deleted.", [
                "name" => $technology->name,
            ]));
    }
}
