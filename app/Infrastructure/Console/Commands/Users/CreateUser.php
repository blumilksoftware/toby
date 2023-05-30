<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;
use ValueError;

class CreateUser extends Command
{
    protected $signature = "toby:create-user {email} {--role=administrator}";
    protected $description = "Create user in non-production environment.";

    public function handle(): void
    {
        if (app()->isProduction()) {
            $this->error("User cannot be created in production environment.");
            return;
        }

        $email = $this->argument("email");

        try {
            Validator::validate(data: ["email" => $email], rules: ["email" => ["email"]]);
        } catch (ValidationException) {
            $this->error("Invalid email.");
            return;
        }

        try {
            $role = Role::from($this->option("role"));
        } catch (ValueError) {
            $this->error("Invalid role.");
            return;
        }

        $userExists = User::query()->where("email", $email)->exists();
        if ($userExists) {
            $this->error("Email already exists.");
            return;
        }

        User::factory([
            "email" => $email,
            "role" => $role,
        ])->create();

        $this->info("User has been created.");
    }
}
