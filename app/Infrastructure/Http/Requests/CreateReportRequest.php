<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "unique:reports,name"],
        ];
    }
}
