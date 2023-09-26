<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $configPermissions = config("permission.permissions");

        foreach ($configPermissions as $permission) {
            Permission::findOrCreate($permission, "web");
        }

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            if (!in_array($permission->name, $configPermissions, true)) {
                $permission->delete();
            }
        }
    }
}
