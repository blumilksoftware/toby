<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentLabelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => [
                "required",
                Rule::unique("equipment_labels", "name")->ignore($this->equipment_label),
                "max:255",
            ],
        ];
    }

    public function data(): array
    {
        return [
            "name" => $this->get("name"),
        ];
    }
}
