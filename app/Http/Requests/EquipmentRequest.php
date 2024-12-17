<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Toby\Helpers\DateFormats;

class EquipmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "idNumber" => ["required", "min:3", "max:80", Rule::unique("equipment_items", "id_number")->ignore($this->equipment_item)],
            "name" => ["required", "min:3", "max:80"],
            "isMobile" => ["required", "boolean"],
            "assignee" => ["required_with:assignedAt", "nullable", "exists:users,id"],
            "assignedAt" => ["required_with:assignee", "nullable", "date_format:" . DateFormats::DATE],
            "labels" => ["array", "distinct"],
        ];
    }

    public function getData(): array
    {
        return [
            "id_number" => $this->get("idNumber"),
            "name" => $this->get("name"),
            "is_mobile" => $this->get("isMobile"),
            "assignee_id" => $this->get("assignee"),
            "assigned_at" => $this->get("assignedAt"),
            "labels" => $this->get("labels"),
        ];
    }
}
