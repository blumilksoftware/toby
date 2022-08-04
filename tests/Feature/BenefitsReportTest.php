<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\Benefit;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\BenefitsReportResource;

class BenefitsReportTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeBenefitsReport(): void
    {
        $admin = User::factory()->admin()->create();

        User::factory(4)->create();
        Benefit::factory(4)->create();

        /** @var BenefitsReport $benefitsReport */
        $benefitsReport = BenefitsReport::factory()->create([
            "name" => "Test Benefits Report",
        ]);

        $benefitsReport->refresh();
        $expectedData = (new BenefitsReportResource($benefitsReport))->response();

        $this->actingAs($admin)
            ->get("/benefits-report/{$benefitsReport->id}")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("BenefitsReport/BenefitsReport")
                    ->where("benefitsReport", $expectedData->getData(true)),
            );

        $this->assertDatabaseHas("reports", [
            "id" => $benefitsReport->id,
            "name" => $benefitsReport->name,
        ]);
    }

    public function testAdminCanCreateBenefitsReport(): void
    {
        $admin = User::factory()->admin()->create();

        Benefit::factory(4)->create();
        User::factory(4)->create();

        $this->actingAs($admin)
            ->post("/benefits-report", [
                "name" => "Test Benefits Report",
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas("reports", [
            "name" => "Test Benefits Report",
        ]);
    }

    public function testAdminCanDownloadBenefitsReport(): void
    {
        $admin = User::factory()->admin()->create();

        [$firstUser, $secondUser] = User::factory(2)->create();
        Benefit::factory(4)->create();

        /** @var BenefitsReport $benefitsReport */
        $benefitsReport = BenefitsReport::factory()->create([
            "name" => "Test BenefitsReport",
        ]);

        $benefitsReport->refresh();
        $expectedFilename = Str::slug($benefitsReport->name) . ".xlsx";

        $this->actingAs($admin)
            ->get("/benefits-report/{$benefitsReport->id}/download?users[]={$firstUser->id}&users[]={$secondUser->id}")
            ->assertDownload($expectedFilename);
    }
}
