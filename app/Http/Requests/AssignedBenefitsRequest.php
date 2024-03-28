<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignedBenefitsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "data" => ["required", "array"],
            "data.*.user" => ["required"],
            "data.*.comment" => ["nullable"],
            "data.*.benefits" => ["nullable", "array"],
            "data.*.benefits.*.id" => ["nullable", "numeric"],
            "data.*.benefits.*.employee" => ["nullable", "numeric", "gte:0"],
            "data.*.benefits.*.employer" => ["nullable", "numeric", "gte:0"],
        ];
    }
}
