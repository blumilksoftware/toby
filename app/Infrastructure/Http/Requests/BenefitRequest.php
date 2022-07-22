<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BenefitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "unique:benefits,name", "max:255"],
            "companion" => ["required", "boolean"],
        ];
    }
}
