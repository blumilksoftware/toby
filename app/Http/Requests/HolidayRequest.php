<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Toby\Helpers\DateFormats;
use Toby\Http\Rules\YearPeriodExists;
use Toby\Models\YearPeriod;

class HolidayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "min:3", "max:150"],
            "date" => ["required",
                "date_format:" . DateFormats::DATE,
                Rule::unique("holidays", "date")->ignore($this->holiday),
                new YearPeriodExists(),
            ],
        ];
    }

    public function data(): array
    {
        $date = $this->get("date");

        return [
            "name" => $this->get("name"),
            "date" => $date,
            "year_period_id" => YearPeriod::findByYear(Carbon::create($date)->year)->id,
        ];
    }
}
