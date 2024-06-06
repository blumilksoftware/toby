<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\SettlementType;
use Toby\Http\Rules\YearPeriodExists;
use Toby\Models\YearPeriod;

class OvertimeRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
            "from" => ["required", "date_format:Y-m-d H:i", new YearPeriodExists()],
            "to" => ["required", "date_format:Y-m-d H:i", new YearPeriodExists()],
            "type" => ["required", new Enum(SettlementType::class)],
            "comment" => ["nullable"],
        ];
    }

    public function data(): array
    {
        return [
            "user_id" => $this->get("user"),
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "settlement_type" => $this->get("type"),
            "year_period_id" => $this->yearPeriod()->id,
            "comment" => $this->get("comment"),
        ];
    }

    public function yearPeriod(): YearPeriod
    {
        return YearPeriod::findByYear(Carbon::create($this->get("from"))->year);
    }
}
