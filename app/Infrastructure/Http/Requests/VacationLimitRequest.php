<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\VacationLimit;

class VacationLimitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "items" => ["required", "array"],
            "items.*.id" => ["required", "exists:vacation_limits,id"],
            "items.*.days" => ["nullable", "integer", "min:0"],
        ];
    }

    public function vacationLimits(): Collection
    {
        return VacationLimit::query()->find($this->collect("items")->pluck("id"));
    }

    public function data(): array
    {
        return $this->collect("items")
            ->keyBy("id")
            ->toArray();
    }
}
