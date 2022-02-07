<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Rules\YearPeriodExists;

class CalculateVacationDaysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "from" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
            "to" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
        ];
    }

    public function from(): Carbon
    {
        return Carbon::create($this->request->get("from"));
    }

    public function to(): Carbon
    {
        return Carbon::create($this->request->get("to"));
    }

    public function yearPeriod(): YearPeriod
    {
        return YearPeriod::findByYear(Carbon::create($this->request->get("from"))->year);
    }
}
