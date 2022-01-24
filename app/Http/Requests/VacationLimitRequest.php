<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Toby\Models\VacationLimit;

class VacationLimitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "items" => ["required", "array"],
            "items.*.id" => ["required", "exists:vacation_limits,id"],
            "items.*.hasVacation" => ["required", "boolean"],
            "items.*.days" => ["exclude_if:items.*.hasVacation,false", "required", "integer", "min:0"],
        ];
    }


    public function vacationLimits(): Collection
    {
        return VacationLimit::query()->find($this->collect("items")->pluck("id"));
    }

    public function data(): Collection
    {
        return $this->collect("items")->mapWithKeys(fn(array $item): array => [
            $item["id"] => [
                "has_vacation" => $item["hasVacation"],
                "days" => $item["days"],
            ]
        ]);
    }
}
