<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class VacationLimitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "items" => ["required", "array"],
            "items.*.user" => ["required", "exists:users,id"],
            "items.*.year" => ["required", "integer"],
            "items.*.days" => ["nullable", "integer", "min:0", "max:100"],
        ];
    }

    public function data(): Collection
    {
        return $this->collect("items");
    }
}
