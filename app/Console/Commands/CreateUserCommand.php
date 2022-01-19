<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Toby\Models\User;

class CreateUserCommand extends Command
{
    protected $signature = "user:create
                            {email : an email for the user}";
    protected $description = "Creates a user";

    public function handle(): void
    {
        $email = $this->argument("email");

        User::factory([
            "email" => $email,
        ])->create();

        $this->info("The user has been created");
    }
}
