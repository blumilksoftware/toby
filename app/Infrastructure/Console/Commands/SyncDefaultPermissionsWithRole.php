<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Domain\Actions\SyncUserPermissionsWithRoleAction;
use Toby\Eloquent\Models\User;

class SyncDefaultPermissionsWithRole extends Command
{
    protected $signature = "toby:sync-permissions";
    protected $description = "Sync default users' permissions with their roles.";

    public function handle(SyncUserPermissionsWithRoleAction $action): void
    {
        User::all()->each(
            fn(User $user): User => $action->execute($user),
        );
    }
}
