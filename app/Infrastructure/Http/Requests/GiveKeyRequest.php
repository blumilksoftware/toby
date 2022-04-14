<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Toby\Eloquent\Models\User;

class GiveKeyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
        ];
    }

    public function recipient(): User
    {
        return User::find($this->get("user"));
    }
}
