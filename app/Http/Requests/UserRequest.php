<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\FormOfEmployment;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "min:3", "max: 150"],
            "email" => ["required", "email", Rule::unique("users", "email")->ignore($this->user)],
            "employmentForm" => ["required", new Enum(FormOfEmployment::class)],
            "employmentDate" => ["required", "date"],
        ];
    }

    public function data(): array
    {
        return [
            "name" => $this->get("name"),
            "email" => $this->get("email"),
            "employment_form" => $this->get("employmentForm"),
            "employment_date" => $this->get("employmentDate"),
        ];
    }
}
