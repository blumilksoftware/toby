<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Toby\Eloquent\Models\Resume;

class ResumeGenerator
{
    public function generate(Resume $resume): string
    {
        $processor = new TemplateProcessor($this->getTemplate());

        $processor->setValue("id", $resume->id);
        $processor->setValue("name", $resume->user ? $resume->user->profile->full_name : $resume->name);

        $this->fillTechnologies($processor, $resume);
        $this->fillLanguages($processor, $resume);
        $this->fillEducation($processor, $resume);
        $this->fillProjects($processor, $resume);

        return $processor->save();
    }

    public function getTemplate(): string
    {
        return resource_path("views/docx/resume_eng.docx");
    }

    protected function fillTechnologies(TemplateProcessor $processor, Resume $resume): void
    {
        $processor->cloneBlock("technologies", 0, true, false, $this->getTechnologies($resume));
    }

    protected function fillLanguages(TemplateProcessor $processor, Resume $resume): void
    {
        $processor->cloneBlock("languages", 0, true, false, $this->getLanguages($resume));
    }

    protected function fillEducation(TemplateProcessor $processor, Resume $resume): void
    {
        $processor->cloneBlock("education", 0, true, false, $this->getEducation($resume));
    }

    protected function fillProjects(TemplateProcessor $processor, Resume $resume): void
    {
        $processor->cloneBlock("projects", $resume->projects->count(), true, true);

        foreach ($resume->projects as $index => $project) {
            ++$index;
            $processor->setValues($this->getProject($project, $index));

            $processor->cloneBlock("project_technologies#{$index}", 0, true, false, $this->getProjectTechnologies($project, $index));
        }
    }

    protected function getProject(array $project, int $index): array
    {
        return [
            "index#{$index}" => $index,
            "start_date#{$index}" => Carbon::createFromFormat("m/Y", $project["startDate"])->format("n.Y"),
            "end_date#{$index}" => $project["current"] ? "present" : Carbon::createFromFormat("m/Y", $project["endDate"])->format("n.Y"),
            "description#{$index}" => $project["description"],
            "tasks#{$index}" => $this->withNewLines($project["tasks"]),
        ];
    }

    protected function withNewLines(string $text): string
    {
        return Str::replace("\n", "</w:t><w:br/><w:t>", $text);
    }

    protected function getProjectTechnologies(array $project, int $index): array
    {
        $technologies = new Collection($project["technologies"] ?? []);

        return $technologies->map(fn(string $name) => [
            "technology#{$index}" => $name,
        ])->all();
    }

    protected function getTechnologies(Resume $resume): array
    {
        return $resume->technologies->map(fn(array $technology): array => [
            "technology_name" => $technology["name"],
            "technology_level" => __("resume.technology_levels.{$technology["level"]}"),
        ])->all();
    }

    protected function getLanguages(Resume $resume): array
    {
        return $resume->languages->map(fn(array $language): array => [
            "language_name" => $language["name"],
            "language_level" => __("resume.language_levels.{$language["level"]}"),
        ])->all();
    }

    protected function getEducation(Resume $resume): array
    {
        return $resume->education->map(fn(array $project, int $index): array => [
            "start_date" => Carbon::createFromFormat("m/Y", $project["startDate"])->format("n.Y"),
            "end_date" => $project["current"] ? "present" : Carbon::createFromFormat("m/Y", $project["endDate"])->format("n.Y"),
            "school" => $project["school"],
            "field_of_study" => $project["fieldOfStudy"],
            "degree" => $project["degree"],
        ])->all();
    }
}
