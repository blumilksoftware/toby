<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\EmploymentForm;
use Toby\Enums\UserHistoryType;

class UserHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "from" => ["required", "date"],
            "to" => ["nullable", "date", "after:from", "required_if:type," . UserHistoryType::MedicalExam->value . "," . UserHistoryType::OhsTraining->value],
            "comment" => ["nullable", "string", "max:255"],
            "type" => ["required", new Enum(UserHistoryType::class)],
            "employmentForm" => [new Enum(EmploymentForm::class), "required_if:type," . UserHistoryType::Employment->value],
        ];
    }

    public function getData(): array
    {
        return [
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "type" => $this->get("type"),
            "employment_form" => $this->get("type") === UserHistoryType::Employment->value ? $this->get("employmentForm") : null,
            "comment" => $this->get("comment"),
            "is_employed_at_current_company" => $this->get("type") === UserHistoryType::Employment->value ? $this->boolean("isEmployedAtCurrentCompany") : null,
        ];
    }
}
