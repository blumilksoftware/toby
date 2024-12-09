<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Toby\Helpers\DateFormats;

class HolidayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "min:3", "max:150"],
            "date" => ["required",
                "date_format:" . DateFormats::DATE,
                Rule::unique("holidays", "date")->ignore($this->holiday),
            ],
        ];
    }

    public function getData(): array
    {
        $date = $this->get("date");

        return [
            "name" => $this->get("name"),
            "date" => $date,
        ];
    }
}
