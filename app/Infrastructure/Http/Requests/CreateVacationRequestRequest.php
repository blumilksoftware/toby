<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Toby\Eloquent\Models\VacationRequest;

class CreateVacationRequestRequest extends FormRequest
{
    protected $redirectRoute = "vacation.requests.create";

    public function authorize(): bool
    {
        return is_null($this->get("user")) ||
            (int)$this->get("user") === $this->user()->id ||
            $this->user()->can("createOnBehalfOfEmployee", VacationRequest::class);
    }

    public function rules(): array
    {
        return [
            "user" => ["nullable", "exists:users,id"],
            "from_date" => ["nullable", "date_format:Y-m-d"],
        ];
    }

    public function data(): array
    {
        return [
            "user" => (int)$this->get('user'),
            "from_date" => $this->get('from_date'),
        ];
    }
}
