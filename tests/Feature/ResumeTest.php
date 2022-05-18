<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Eloquent\Models\Resume;
use Toby\Eloquent\Models\Technology;
use Toby\Eloquent\Models\User;

class ResumeTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        Technology::factory()->createMany([
            ["name" => "Laravel"],
            ["name" => "Symfony"],
        ]);
    }

    public function testAdminCanSeeResumesList(): void
    {
        Resume::factory()->count(10)->create();
        $admin = User::factory()->admin()->create();

        $this->assertDatabaseCount("resumes", 10);

        $this->actingAs($admin)
            ->get("/resumes")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Resumes/Index")
                    ->has("resumes.data", 10),
            );
    }

    public function testAdminCanCreateResumeForEmployee(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->hasProfile([
            "first_name" => "Jan",
            "last_name" => "Kowalski",
            "employment_form" => EmploymentForm::EmploymentContract,
            "position" => "user",
            "employment_date" => Carbon::createFromDate(2021, 1, 4),
        ])->create();

        $this->actingAs($admin)
            ->post("/resumes", [
                "user" => $user->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("resumes", [
            "user_id" => $user->id,
        ]);
    }

    public function testAdminCanCreateResumeForSomebodyWhoDoesNotExistInTheDatabase(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post("/resumes", [
                "name" => "Anna Nowak",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("resumes", [
            "name" => "Anna Nowak",
        ]);
    }

    public function testAdminCanEditResume(): void
    {
        $admin = User::factory()->admin()->create();
        $resume = Resume::factory([
            "name" => "Anna Nowak",
            "education" => [
                "school" => "Testowa Szkoła",
                "degree" => "inżynier",
                "fieldOfStudy" => "Informatyka",
                "current" => false,
                "startDate" => Carbon::createFromDate(2017, 9)->format("m/Y"),
                "endDate" => Carbon::createFromDate(2021, 3)->format("m/Y"),
            ],
            "languages" => [
                "name" => "English",
                "level" => "C2",
            ],
            "technologies" => [
                "name" => "Laravel",
                "level" => "Expert",
            ],
            "projects" => [
                "description" => "Test project",
                "technologies" => Technology::all()->pluck("name"),
                "current" => false,
                "startDate" => Carbon::createFromDate(2021, 3)->format("m/Y"),
                "endDate" => Carbon::createFromDate(2022, 1)->format("m/Y"),
                "tasks" => "Tasks",
            ],
        ])->create();

        $this->actingAs($admin)
            ->put("/resumes/{$resume->id}", [
                "name" => "Natalia Kowalska",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("resumes", [
            "name" => "Natalia Kowalska",
        ]);
    }

    public function testAdminCanGenerateResume(): void
    {
        $resume = Resume::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get("/resumes/{$resume->id}")
            ->assertDownload("resume-{$resume->id}.docx");
    }

    public function testAdminCanDeleteResume(): void
    {
        $resume = Resume::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete("/resumes/{$resume->id}")
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($resume);
    }
}
