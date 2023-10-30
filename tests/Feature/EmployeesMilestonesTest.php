<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Domain\EmployeesMilestonesRetriever;
use Toby\Eloquent\Models\User;

class EmployeesMilestonesTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected EmployeesMilestonesRetriever $employeesMilestonesRetriever;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeesMilestonesRetriever = $this->app->make(EmployeesMilestonesRetriever::class);
    }

    public function testUserCanSeeEmployeesMilestonesList(): void
    {
        $user = User::factory()->create();

        User::factory()->count(9)->create();

        $this->actingAs($user)
            ->get("/employees-milestones")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("EmployeesMilestones")
                    ->has("users.data", 10),
            );
    }

    public function testSortingByBirthdays(): void
    {
        $user1 = User::factory()
            ->hasProfile(["birthday" => Carbon::createFromDate(1998, 1, 1)])
            ->employee()
            ->create();

        $user2 = User::factory()
            ->hasProfile(["birthday" => Carbon::createFromDate(2000, 12, 30)])
            ->employee()
            ->create();

        $user3 = User::factory()
            ->hasProfile(["birthday" => Carbon::createFromDate(1997, 5, 22)])
            ->employee()
            ->create();

        $sortedUsersByUpcomingBirthday = $this->employeesMilestonesRetriever->getResults(null, "birthday-asc")->values();

        $this->assertEquals($user1->id, $sortedUsersByUpcomingBirthday[0]->id);
        $this->assertEquals($user3->id, $sortedUsersByUpcomingBirthday[1]->id);
        $this->assertEquals($user2->id, $sortedUsersByUpcomingBirthday[2]->id);

        $sortedUsersByFurthestBirthday = $this->employeesMilestonesRetriever->getResults(null, "birthday-desc")->values();

        $this->assertEquals($user2->id, $sortedUsersByFurthestBirthday[0]->id);
        $this->assertEquals($user3->id, $sortedUsersByFurthestBirthday[1]->id);
        $this->assertEquals($user1->id, $sortedUsersByFurthestBirthday[2]->id);
    }

    public function testSortingBySeniority(): void
    {
        $user1 = User::factory()
            ->hasProfile(["employment_date" => Carbon::createFromDate(2023, 1, 31)])
            ->employee()
            ->create();

        $user2 = User::factory()
            ->hasProfile(["employment_date" => Carbon::createFromDate(2022, 1, 1)])
            ->employee()
            ->create();

        $user3 = User::factory()
            ->hasProfile(["employment_date" => Carbon::createFromDate(2021, 10, 4)])
            ->employee()
            ->create();

        $sortedUsersByLongestSeniority = $this->employeesMilestonesRetriever->getResults(null, "seniority-asc")->values();

        $this->assertEquals($user3->id, $sortedUsersByLongestSeniority[0]->id);
        $this->assertEquals($user2->id, $sortedUsersByLongestSeniority[1]->id);
        $this->assertEquals($user1->id, $sortedUsersByLongestSeniority[2]->id);

        $sortedUsersByShortestSeniority = $this->employeesMilestonesRetriever->getResults(null, "seniority-desc")->values();

        $this->assertEquals($user1->id, $sortedUsersByShortestSeniority[0]->id);
        $this->assertEquals($user2->id, $sortedUsersByShortestSeniority[1]->id);
        $this->assertEquals($user3->id, $sortedUsersByShortestSeniority[2]->id);
    }
}
