<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\ResumeGenerator;
use Toby\Http\Requests\ResumeRequest;
use Toby\Http\Resources\ResumeFormResource;
use Toby\Http\Resources\ResumeResource;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\Resume;
use Toby\Models\Technology;
use Toby\Models\User;

class ResumeController extends Controller
{
    public function index(): Response
    {
        $this->authorize("manageResumes");

        $resumes = Resume::query()
            ->with("user")
            ->whereRelation("user", fn(Builder $query): Builder => $query->withTrashed(false))
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

    public function show(Resume $resume, ResumeGenerator $generator): BinaryFileResponse
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
