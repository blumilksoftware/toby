<?php

declare(strict_types=1);

use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;

return [
    VacationType::Vacation->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::OnRequest->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::TimeInLieu->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Sick->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Unpaid->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Special->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Childcare->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Training->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Volunteering->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Volunteering->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
    ],
    VacationType::Absence->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::CommissionContract,
            EmploymentForm::B2bContract,
            EmploymentForm::BoardMemberContract,
        ],
    ],
];
