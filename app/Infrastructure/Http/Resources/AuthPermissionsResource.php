<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AuthPermissionsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $permissions = Permission::all();

        return $permissions->mapWithKeys(
            fn(Permission $permission): array => [
                Str::camel($permission->name) => $this->resource ? $this->resource->hasPermissionTo($permission) : false,
            ],
        )->toArray();
    }
}
