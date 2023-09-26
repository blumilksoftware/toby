<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config("permission.permissions") as $permission) {
            Permission::create(
                [
                    "name" => $permission,
                ],
            );
        }
    }
}
