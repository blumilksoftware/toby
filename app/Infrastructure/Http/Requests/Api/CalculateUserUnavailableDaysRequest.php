<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalculateUserUnavailableDaysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
        ];
    }
}
