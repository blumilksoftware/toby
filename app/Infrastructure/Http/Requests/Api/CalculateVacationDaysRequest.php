<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Rules\YearPeriodExists;

class CalculateVacationDaysRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "vacationType" => ["required", new Enum(VacationType::class)],
            "from" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
            "to" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
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

    public function yearPeriod(): YearPeriod
    {
        return YearPeriod::findByYear(Carbon::create($this->request->get("from"))->year);
    }
}
