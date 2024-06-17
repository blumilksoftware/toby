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
            "to" => ["date", "after:from"],
            "type" => ["required", new Enum(UserHistoryType::class)],
            "employmentForm" => [new Enum(EmploymentForm::class), "required_if:type," . UserHistoryType::Employment->value],
        ];
    }

    public function data(): array
    {
        return [
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "type" => $this->get("type"),
            "employment_form" => $this->get("type") === UserHistoryType::Employment->value ? $this->get("employmentForm") : null,
        ];
    }
}
