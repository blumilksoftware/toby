<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BenefitsReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "unique:reports,name", "max:255"],
        ];
    }
}
