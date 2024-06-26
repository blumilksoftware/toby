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
            "items.*.days" => ["nullable", "integer", "min:0", "max:100"],
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
