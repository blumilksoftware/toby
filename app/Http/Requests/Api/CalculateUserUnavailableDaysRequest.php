<?php

declare(strict_types=1);

namespace Toby\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\VacationType;

class CalculateUserUnavailableDaysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "vacationType" => [new Enum(VacationType::class)],
            "user" => ["required", "exists:users,id"],
            "year" => ["required", "integer"],
        ];
    }

    public function vacationType(): ?VacationType
    {
        return $this->request->has("vacationType")
            ? VacationType::from($this->request->get("vacationType"))
            : null;
    }

    public function getYear(): int
    {
        return $this->integer("year", Carbon::now()->year);
    }
}
