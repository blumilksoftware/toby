<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechnologyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => [
                "required",
                Rule::unique("technologies", "name")->ignore($this->technology),
                "max:255",
            ],
        ];
    }

    public function data(): array
    {
        return [
            "name" => $this->get("name"),
        ];
    }
}
