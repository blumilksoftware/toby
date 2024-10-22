<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TakeDaysFromLastYearRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
            "year" => ["required", "integer"],
            "days" => ["required", "integer"],
        ];
    }

    public function getData(): array
    {
        return $this->only(["user", "year", "days"]);
    }
}
