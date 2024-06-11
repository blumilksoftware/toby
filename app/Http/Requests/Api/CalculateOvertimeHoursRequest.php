<?php

declare(strict_types=1);

namespace Toby\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Toby\Helpers\DateFormats;
use Toby\Http\Rules\YearPeriodExists;
use Toby\Models\YearPeriod;

class CalculateOvertimeHoursRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "from" => ["required", "date_format:" . DateFormats::DATETIME, new YearPeriodExists()],
            "to" => ["required", "date_format:" . DateFormats::DATETIME, new YearPeriodExists()],
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
