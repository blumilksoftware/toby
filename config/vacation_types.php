<?php

declare(strict_types=1);

use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;

return [
    VacationType::VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
    ],
    VacationType::VACATION_ON_REQUEST->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => true,
    ],
    VacationType::TIME_IN_LIEU->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::SICK_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => false,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::UNPAID_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::SPECIAL_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::CHILDCARE_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => false,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::TRAINING_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
    VacationType::VOLUNTEERING_VACATION->value => [
        VacationTypeConfigRetriever::KEY_TECHNICAL_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_ADMINISTRATIVE_APPROVAL => true,
        VacationTypeConfigRetriever::KEY_BILLABLE => true,
        VacationTypeConfigRetriever::KEY_HAS_LIMIT => false,
    ],
];
