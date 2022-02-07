<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "firstName" => ["required", "min:3", "max:80"],
            "lastName" => ["required", "min:3", "max:80"],
            "email" => ["required", "email", Rule::unique("users", "email")->ignore($this->user)],
            "role" => ["required", new Enum(Role::class)],
            "position" => ["required"],
            "employmentForm" => ["required", new Enum(EmploymentForm::class)],
            "employmentDate" => ["required", "date_format:Y-m-d"],
        ];
    }

    public function data(): array
    {
        return [
            "first_name" => $this->get("firstName"),
            "last_name" => $this->get("lastName"),
            "email" => $this->get("email"),
            "position" => $this->get("position"),
            "role" => $this->get("role"),
            "employment_form" => $this->get("employmentForm"),
            "employment_date" => $this->get("employmentDate"),
        ];
    }
}
