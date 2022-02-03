<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Toby\Domain\Enums\VacationType;
use Toby\Infrastructure\Http\Rules\YearPeriodExists;

class VacationRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "type" => ["required", new Enum(VacationType::class)],
            "from" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
            "to" => ["required", "date_format:Y-m-d", new YearPeriodExists()],
            "comment" => ["nullable"],
        ];
    }

    public function data(): array
    {
        return [
            "type" => $this->get("type"),
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "comment" => $this->get("comment"),
        ];
    }
}
