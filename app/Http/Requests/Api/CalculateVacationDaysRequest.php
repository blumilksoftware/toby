<?php

declare(strict_types=1);

namespace Toby\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\VacationType;
use Toby\Helpers\DateFormats;

class CalculateVacationDaysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "vacationType" => ["required", new Enum(VacationType::class)],
            "from" => ["required", "date_format:" . DateFormats::DATE],
            "to" => ["required", "date_format:" . DateFormats::DATE],
        ];
    }

    public function vacationType(): VacationType
    {
        return VacationType::from($this->request->get("vacationType"));
    }

    public function from(): Carbon
    {
        return Carbon::create($this->request->get("from"));
    }

    public function to(): Carbon
    {
        return Carbon::create($this->request->get("to"));
    }
}
