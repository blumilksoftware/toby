<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse as BinaryFileResponseAlias;
use Toby\Domain\ResumeGenerator;
use Toby\Eloquent\Models\Resume;
use Toby\Eloquent\Models\Technology;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\ResumeRequest;
use Toby\Infrastructure\Http\Resources\ResumeFormResource;
use Toby\Infrastructure\Http\Resources\ResumeResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class ResumeController extends Controller
{
    public function index(): Response
    {
        $this->authorize("manageResumes");

        $resumes = Resume::query()
            ->latest("updated_at")
            ->paginate();

        return inertia("Resumes/Index", [
            "resumes" => ResumeResource::collection($resumes),
        ]);
    }

    public function create(): Response
    {
        $this->authorize("manageResumes");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Resumes/Create", [
            "users" => SimpleUserResource::collection($users),
            "technologies" => Technology::all()->pluck("name"),
        ]);
    }

    public function show(Resume $resume, ResumeGenerator $generator): BinaryFileResponseAlias
    {
        $this->authorize("manageResumes");

        $path = $generator->generate($resume);

        return response()
            ->download($path, "resume-{$resume->id}.docx")
            ->deleteFileAfterSend();
    }

    public function store(ResumeRequest $request): RedirectResponse
    {
        $this->authorize("manageResumes");

        $resume = new Resume();

        if ($request->hasEmployee()) {
            $resume->user()->associate($request->getEmployee());
        } else {
            $resume->name = $request->getName();
        }

        $resume->fill([
            "education" => $request->getEducation(),
            "languages" => $request->getLanguageLevels(),
            "technologies" => $request->getTechnologyLevels(),
            "projects" => $request->getProjects(),
        ]);

        $resume->save();

        return redirect()
            ->route("resumes.index")
            ->with("success", __("Resume created."));
    }

    public function edit(Resume $resume): Response
    {
        $this->authorize("manageResumes");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Resumes/Edit", [
            "resume" => new ResumeFormResource($resume),
            "users" => SimpleUserResource::collection($users),
            "technologies" => Technology::all()->pluck("name"),
        ]);
    }

    public function update(Resume $resume, ResumeRequest $request): RedirectResponse
    {
        $this->authorize("manageResumes");

        if ($request->hasEmployee()) {
            $resume->user()->associate($request->getEmployee());
        } else {
            $resume->user()->dissociate();
            $resume->name = $request->getName();
        }

        $resume->fill([
            "education" => $request->getEducation(),
            "languages" => $request->getLanguageLevels(),
            "technologies" => $request->getTechnologyLevels(),
            "projects" => $request->getProjects(),
        ]);

        $resume->save();

        return redirect()
            ->route("resumes.index")
            ->with("success", __("Resume updated."));
    }

    public function destroy(Resume $resume): RedirectResponse
    {
        $this->authorize("manageResumes");

        $resume->delete();

        return redirect()
            ->route("resumes.index")
            ->with("success", __("Resume deleted."));
    }
}
