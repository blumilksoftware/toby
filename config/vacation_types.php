<?php

declare(strict_types=1);

use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;

return [
    VacationType::Vacation->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
    ],
    VacationType::OnRequest->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
    ],
    VacationType::TimeInLieu->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Sick->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Unpaid->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Special->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Childcare->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Training->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::Volunteering->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
];
