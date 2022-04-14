<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Toby\Eloquent\Models\User;

class MoveUserDataToProfile extends Command
{
    protected $signature = "toby:move-user-data-to-profile";
    protected $description = "Move user data to their profiles";

    public function handle(): void
    {
        $users = User::all();

        /** @var User $user */
        foreach ($users as $user) {
            $user->profile()->updateOrCreate(["user_id" => $user->id], [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "position" => $user->position,
                "employment_form" => $user->employment_form,
                "employment_date" => $user->employment_date,
            ]);
        }
    }
}
