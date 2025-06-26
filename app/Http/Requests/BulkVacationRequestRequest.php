<?php

declare(strict_types=1);

namespace Toby\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;
use Toby\Enums\VacationType;
use Toby\Helpers\DateFormats;
use Toby\Models\User;

class BulkVacationRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "users" => ["required", "exists:users,id"],
            "type" => ["required", new Enum(VacationType::class)],
            "from" => ["required", "date_format:" . DateFormats::DATE],
            "to" => ["required", "date_format:" . DateFormats::DATE],
            "flowSkipped" => ["nullable", "boolean"],
            "comment" => ["nullable"],
        ];
    }

    public function getUsers(): Collection
    {
        $users = $this->collect("users");

        return User::query()->whereKey($users)->get();
    }

    public function getData(): array
    {
        return [
            "type" => $this->get("type"),
            "from" => $this->get("from"),
            "to" => $this->get("to"),
            "comment" => $this->get("comment"),
            "flow_skipped" => $this->boolean("flowSkipped"),
        ];
    }

    public function wantsSkipFlow(): bool
    {
        return $this->boolean("flowSkipped");
    }
}
