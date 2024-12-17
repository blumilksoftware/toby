<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\SettlementType;
use Toby\Helpers\DateFormats;

class OvertimeRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
            "from" => ["required", "date_format:" . DateFormats::DATETIME],
            "to" => ["required", "date_format:" . DateFormats::DATETIME, "after:from"],
            "type" => ["required", new Enum(SettlementType::class)],
            "comment" => ["nullable"],
        ];
    }

    public function getData(): array
    {
        return [
            "user_id" => $this->get("user"),
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "settlement_type" => $this->get("type"),
            "comment" => $this->get("comment"),
        ];
    }
}
