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
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::OnRequest->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::TimeInLieu->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Sick->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Unpaid->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Special->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Childcare->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Training->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    VacationType::Volunteering->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Volunteering->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::Absence->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::CommissionContract,
            EmploymentForm::B2bContract,
            EmploymentForm::BoardMemberContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => true,
    ],
    VacationType::RemoteWork->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
        VacationTypeConfigRetriever::KEY_AVAILABLE_FOR => [
            EmploymentForm::EmploymentContract,
            EmploymentForm::CommissionContract,
            EmploymentForm::B2bContract,
            EmploymentForm::BoardMemberContract,
        ],
        VacationTypeConfigRetriever::KEY_IS_VACATION => false,
    ],
];
