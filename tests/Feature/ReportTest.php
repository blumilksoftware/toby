<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\Report;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\ReportResource;

class ReportTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeReport(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory(4)->create();
        Benefit::factory(4)->create();

        /** @var Report $report */
        $report = Report::factory()->create([
            "name" => "Test Report",
        ]);

        $report->refresh();
        $expectedData = (new ReportResource($report))->response();

        $this->actingAs($admin)
            ->get("/benefits-report/{$report->id}")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Report/Report")
                    ->where("report", $expectedData->getData(true)),
            );

        $this->assertDatabaseHas("reports", [
            "id" => $report->id,
            "name" => $report->name,
        ]);
    }

    public function testAdminCanCreateReport(): void
    {
        $admin = User::factory()->admin()->create();

        Benefit::factory(4)->create();
        User::factory(4)->create();

        Report::factory()->create([
            "name" => "current",
            "users" => null,
            "benefits" => null,
            "committed_at" => null,
        ]);

        $this->actingAs($admin)
            ->post("/benefits-report", [
                "name" => "Test Report",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("reports", [
            "name" => "Test Report",
        ]);
    }

    public function testAdminCanDownloadReport(): void
    {
        $admin = User::factory()->admin()->create();

        [$firstUser, $secondUser] = User::factory(2)->create();
        Benefit::factory(4)->create();

        /** @var Report $report */
        $report = Report::factory()->create([
            "name" => "Test Report",
        ]);

        $report->refresh();
        $expectedFilename = Str::slug($report->name) . ".xlsx";

        $this->actingAs($admin)
            ->get("/benefits-report/{$report->id}/download?users[]={$firstUser->id}&users[]={$secondUser->id}")
            ->assertDownload($expectedFilename);
    }
}
