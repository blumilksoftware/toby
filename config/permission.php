<?php

declare(strict_types=1);

use Spatie\Permission\Models\Permission;
use Toby\Domain\Enums\Role;

return [
    "models" => [
        "permission" => Permission::class,
        "role" => Spatie\Permission\Models\Role::class,
    ],
    "table_names" => [
        "roles" => "roles",
        "permissions" => "permissions",
        "model_has_permissions" => "model_has_permissions",
        "model_has_roles" => "model_has_roles",
        "role_has_permissions" => "role_has_permissions",
    ],
    "column_names" => [
        "role_pivot_key" => null,
        "permission_pivot_key" => null,
        "model_morph_key" => "model_id",
        "team_foreign_key" => "team_id",
    ],
    "register_permission_check_method" => true,
    "teams" => false,
    "display_permission_in_exception" => false,
    "display_role_in_exception" => false,
    "enable_wildcard_permission" => false,
    "cache" => [
        "expiration_time" => DateInterval::createFromDateString("24 hours"),
        "key" => "spatie.permission.cache",
        "store" => "default",
    ],
    "permissions" => [
        "managePermissions",
        "manageHolidays",
        "manageUsers",
        "manageKeys",
        "manageTechnologies",
        "manageResumes",
        "manageBenefits",
        "manageVacationLimits",
        "manageRequestsAsAdministrativeApprover",
        "manageRequestsAsTechnicalApprover",
        "createRequestsOnBehalfOfEmployee",
        "listMonthlyUsage",
        "listAllRequests",
        "skipRequestFlow",
        "receiveUpcomingAndOverdueMedicalExamsNotification",
        "receiveUpcomingAndOverdueOhsTrainingNotification",
        "receiveVacationRequestsSummaryNotification",
        "receiveVacationRequestWaitsForApprovalNotification",
        "receiveVacationRequestStatusChangedNotification",
        "receiveBenefitsReportCreationNotification",
        "manageEquipment",
    ],
    "permission_roles" => [
        Role::Administrator->value => [
            "managePermissions",
            "manageHolidays",
            "manageUsers",
            "manageKeys",
            "manageTechnologies",
            "manageResumes",
            "manageBenefits",
            "manageVacationLimits",
            "manageRequestsAsAdministrativeApprover",
            "manageRequestsAsTechnicalApprover",
            "createRequestsOnBehalfOfEmployee",
            "listMonthlyUsage",
            "listAllRequests",
            "skipRequestFlow",
            "receiveVacationRequestsSummaryNotification",
            "receiveVacationRequestWaitsForApprovalNotification",
            "receiveVacationRequestStatusChangedNotification",
            "manageEquipment",
        ],
        Role::AdministrativeApprover->value => [
            "managePermissions",
            "manageHolidays",
            "manageUsers",
            "manageKeys",
            "manageTechnologies",
            "manageResumes",
            "manageBenefits",
            "manageVacationLimits",
            "manageRequestsAsAdministrativeApprover",
            "createRequestsOnBehalfOfEmployee",
            "listMonthlyUsage",
            "listAllRequests",
            "skipRequestFlow",
            "receiveUpcomingAndOverdueMedicalExamsNotification",
            "receiveUpcomingAndOverdueOhsTrainingNotification",
            "receiveVacationRequestsSummaryNotification",
            "receiveVacationRequestWaitsForApprovalNotification",
            "receiveVacationRequestStatusChangedNotification",
            "receiveBenefitsReportCreationNotification",
            "manageEquipment",
        ],
        Role::TechnicalApprover->value => [
            "manageTechnologies",
            "manageResumes",
            "manageRequestsAsTechnicalApprover",
            "listAllRequests",
            "receiveVacationRequestsSummaryNotification",
            "receiveVacationRequestWaitsForApprovalNotification",
            "receiveVacationRequestStatusChangedNotification",
        ],
        Role::Employee->value => [],
    ],
];
