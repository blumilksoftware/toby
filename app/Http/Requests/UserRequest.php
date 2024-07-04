<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Role;
use Toby\Helpers\DateFormats;

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
            "employmentDate" => ["required", "date_format:" . DateFormats::DATE],
            "birthday" => ["required", "date_format:" . DateFormats::DATE, "before:today"],
            "slackId" => [],
        ];
    }

    public function userData(): array
    {
        return [
            "email" => $this->get("email"),
            "role" => $this->get("role"),
        ];
    }

    public function profileData(): array
    {
        return [
            "first_name" => $this->get("firstName"),
            "last_name" => $this->get("lastName"),
            "position" => $this->get("position"),
            "employment_form" => $this->get("employmentForm"),
            "employment_date" => $this->get("employmentDate"),
            "birthday" => $this->get("birthday"),
            "slack_id" => $this->get("slackId"),
        ];
    }
}
