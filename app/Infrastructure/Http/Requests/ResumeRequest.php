<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Toby\Eloquent\Models\User;

class ResumeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["nullable", "exists:users,id"],
            "name" => ["required_without:user"],

            "education.*.school" => ["required"],
            "education.*.degree" => ["required"],
            "education.*.fieldOfStudy" => ["required"],
            "education.*.startDate" => ["required", "date_format:Y-m-d"],
            "education.*.endDate" => ["required", "date_format:Y-m-d", "after:startDate"],

            "languages.*.name" => ["required", "distinct"],
            "languages.*.level" => ["required", Rule::in(1, 2, 3, 4, 5, 6)],

            "technologies.*.name" => ["required", "exists:technologies,name", "distinct"],
            "technologies.*.level" => ["required", Rule::in(1, 2, 3, 4, 5)],

            "projects.*.description" => ["required"],
            "projects.*.technologies" => ["required"],
            "projects.*.startDate" => ["required", "date_format:Y-m-d"],
            "projects.*.endDate" => ["required", "date_format:Y-m-d", "after:startDate"],
            "projects.*.tasks" => ["required"],
        ];
    }

    public function hasEmployee(): bool
    {
       return $this->has("user");
    }

    public function getEmployee(): User
    {
        /** @var User $user */
        $user = User::query()->find($this->get("user"));

        return $user;
    }

    public function getName(): string
    {
        return $this->get("name");
    }

    public function getLanguageLevels(): Collection
    {
        return $this->collect("languages");
    }

    public function getTechnologyLevels(): Collection
    {
        return $this->collect("technologies");
    }

    public function getEducation(): Collection
    {
        return $this->collect("education");
    }

    public function getProjects(): Collection
    {
        return $this->collect("projects");
    }
}
