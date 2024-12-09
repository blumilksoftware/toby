<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\VacationType;
use Toby\Helpers\DateFormats;

class VacationRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "user" => ["required", "exists:users,id"],
            "type" => ["required", new Enum(VacationType::class)],
            "from" => ["required", "date_format:" . DateFormats::DATE],
            "to" => ["required", "date_format:" . DateFormats::DATE],
            "flowSkipped" => ["nullable", "boolean"],
            "comment" => ["nullable"],
        ];
    }

    public function getData(): array
    {
        return [
            "user_id" => $this->get("user"),
            "type" => $this->get("type"),
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "comment" => $this->get("comment"),
            "flow_skipped" => $this->boolean("flowSkipped"),
        ];
    }

    public function createsOnBehalfOfEmployee(): bool
    {
        return $this->user()->id !== $this->get("user");
    }

    public function wantsSkipFlow(): bool
    {
        return $this->boolean("flowSkipped");
    }
}
