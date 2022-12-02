<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TakeDaysFromLastYearRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "days" => ["required", "integer"],
        ];
    }

    public function getDays(): int
    {
        return $this->integer("days");
    }
}
