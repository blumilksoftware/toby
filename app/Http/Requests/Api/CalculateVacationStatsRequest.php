<?php

declare(strict_types=1);

namespace Toby\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CalculateVacationStatsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
            "year" => ["required", "integer"],
        ];
    }

    public function getYear(): int
    {
        return $this->integer("year", Carbon::now()->year);
    }
}
