<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Models\User;

class PermissionTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdminCanSeeEditEmployeePermissionsForm(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create();

        $this->actingAs($admin)
            ->get("/users/{$employee->id}/permissions")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has(
                        "permissions",
                        fn(Assert $page) => $page
                            ->where("managePermissions", false)
                            ->where("manageHolidays", false)
                            ->where("showInactiveUsers", false)
                            ->where("manageUsers", false)
                            ->where("manageKeys", false)
                            ->where("manageTechnologies", false)
                            ->where("manageResumes", false)
                            ->where("manageBenefits", false)
                            ->where("manageVacationLimits", false)
                            ->where("manageRequestsAsAdministrativeApprover", false)
                            ->where("manageRequestsAsTechnicalApprover", false)
                            ->where("manageOvertimeAsAdministrativeApprover", false)
                            ->where("manageOvertimeAsTechnicalApprover", false)
                            ->where("createRequestsOnBehalfOfEmployee", false)
                            ->where("cancelRequestsAsTechnicalApprover", false)
                            ->where("listMonthlyUsage", false)
                            ->where("listAllRequests", false)
                            ->where("listAllOvertimeRequests", false)
                            ->where("skipRequestFlow", false)
                            ->where("receiveUpcomingAndOverdueMedicalExamsNotification", false)
                            ->where("receiveUpcomingAndOverdueOhsTrainingNotification", false)
                            ->where("receiveBenefitsReportCreationNotification", false)
                            ->where("receiveVacationRequestsSummaryNotification", false)
                            ->where("receiveVacationRequestWaitsForApprovalNotification", false)
                            ->where("receiveVacationRequestStatusChangedNotification", false)
                            ->where("manageEquipment", false)
                            ->where("receiveOvertimeRequestsSummaryNotification", false)
                            ->where("downloadWorkHoursSummary", false)
                            ->where("downloadOvertimeSummary", false),
                    ),
            );
    }

    public function testAdminCanSeeEditTechnicalApproverPermissionsForm(): void
    {
        $admin = User::factory()->admin()->create();
        $technicalApprover = User::factory()->technicalApprover()->create();

        $this->actingAs($admin)
            ->get("/users/{$technicalApprover->id}/permissions")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has(
                        "permissions",
                        fn(Assert $page) => $page
                            ->where("managePermissions", false)
                            ->where("manageHolidays", false)
                            ->where("showInactiveUsers", false)
                            ->where("manageUsers", false)
                            ->where("manageKeys", false)
                            ->where("manageTechnologies", true)
                            ->where("manageResumes", true)
                            ->where("manageBenefits", false)
                            ->where("manageVacationLimits", false)
                            ->where("manageRequestsAsAdministrativeApprover", false)
                            ->where("manageRequestsAsTechnicalApprover", true)
                            ->where("cancelRequestsAsTechnicalApprover", true)
                            ->where("manageOvertimeAsAdministrativeApprover", false)
                            ->where("manageOvertimeAsTechnicalApprover", true)
                            ->where("createRequestsOnBehalfOfEmployee", false)
                            ->where("listMonthlyUsage", false)
                            ->where("listAllRequests", true)
                            ->where("listAllOvertimeRequests", true)
                            ->where("skipRequestFlow", false)
                            ->where("receiveUpcomingAndOverdueMedicalExamsNotification", false)
                            ->where("receiveUpcomingAndOverdueOhsTrainingNotification", false)
                            ->where("receiveBenefitsReportCreationNotification", false)
                            ->where("receiveVacationRequestsSummaryNotification", true)
                            ->where("receiveVacationRequestWaitsForApprovalNotification", true)
                            ->where("receiveVacationRequestStatusChangedNotification", true)
                            ->where("manageEquipment", false)
                            ->where("receiveOvertimeRequestsSummaryNotification", true)
                            ->where("downloadWorkHoursSummary", false)
                            ->where("downloadOvertimeSummary", false),
                    ),
            );
    }

    public function testAdminCanSeeEditAdministrativeApproverPermissionsForm(): void
    {
        $admin = User::factory()->admin()->create();
        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->actingAs($admin)
            ->get("/users/{$administrativeApprover->id}/permissions")
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->has(
                        "permissions",
                        fn(Assert $page) => $page
                            ->where("managePermissions", true)
                            ->where("manageHolidays", true)
                            ->where("showInactiveUsers", false)
                            ->where("manageUsers", true)
                            ->where("manageKeys", true)
                            ->where("manageTechnologies", true)
                            ->where("manageResumes", true)
                            ->where("manageBenefits", true)
                            ->where("manageVacationLimits", true)
                            ->where("manageRequestsAsAdministrativeApprover", true)
                            ->where("manageRequestsAsTechnicalApprover", false)
                            ->where("manageOvertimeAsAdministrativeApprover", true)
                            ->where("manageOvertimeAsTechnicalApprover", false)
                            ->where("cancelRequestsAsTechnicalApprover", false)
                            ->where("createRequestsOnBehalfOfEmployee", true)
                            ->where("listMonthlyUsage", true)
                            ->where("listAllRequests", true)
                            ->where("listAllOvertimeRequests", true)
                            ->where("skipRequestFlow", true)
                            ->where("receiveUpcomingAndOverdueMedicalExamsNotification", true)
                            ->where("receiveUpcomingAndOverdueOhsTrainingNotification", true)
                            ->where("receiveBenefitsReportCreationNotification", true)
                            ->where("receiveVacationRequestsSummaryNotification", true)
                            ->where("receiveVacationRequestWaitsForApprovalNotification", true)
                            ->where("receiveVacationRequestStatusChangedNotification", true)
                            ->where("manageEquipment", true)
                            ->where("receiveOvertimeRequestsSummaryNotification", false)
                            ->where("downloadWorkHoursSummary", true)
                            ->where("downloadOvertimeSummary", true),
                    ),
            );
    }

    public function testEmployeeCannotSeeEditPermissionsForm(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get("/users/{$employee->id}/permissions")
            ->assertForbidden();
    }

    public function testAnyUserWithProperPermissionSeeEditPermissionsForm(): void
    {
        $employee = User::factory()->employee()->create();

        $employee->givePermissionTo("managePermissions");

        $this->actingAs($employee)
            ->get("/users/{$employee->id}/permissions")
            ->assertOk();
    }

    public function testAdminCanEditPermissions(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create();

        $this->assertFalse($employee->can("manageBenefits"));

        $this->actingAs($admin)
            ->patch("/users/{$employee->id}/permissions", [
                "permissions" => [
                    "manageBenefits" => true,
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue($employee->refresh()->can("manageBenefits"));
    }

    public function testEmployeeCannotEditPermissions(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->patch("/users/{$employee->id}/permissions", [
                "permissions" => [
                    "manageBenefits" => true,
                ],
            ])
            ->assertForbidden();
    }

    public function testAnyUserWithProperPermissionCanEditPermissions(): void
    {
        /** @var User $employee */
        $employee = User::factory()->employee()->create();

        $employee->givePermissionTo("managePermissions");

        $this->assertTrue($employee->can("managePermissions"));
        $this->assertFalse($employee->can("manageBenefits"));

        $this->actingAs($employee)
            ->patch("/users/{$employee->id}/permissions", [
                "permissions" => [
                    "manageBenefits" => true,
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue($employee->refresh()->can("manageBenefits"));
    }
}
