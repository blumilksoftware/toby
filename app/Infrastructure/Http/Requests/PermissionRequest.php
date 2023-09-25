<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Permission;

class PermissionRequest extends FormRequest
{
    public function rules(): array
    {
        $availablePermissions = Permission::all()->pluck("name");

        return [
            "permissions" => ["required", "array:{$availablePermissions->implode(",")}"],
            "permissions.*" => ["required", "boolean"],
        ];
    }
}
