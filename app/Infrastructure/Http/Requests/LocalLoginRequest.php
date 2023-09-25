<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocalLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email"],
            "password" => ["required"],
        ];
    }
}
