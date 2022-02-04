<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Domain\Enums\Role;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("users", function (Blueprint $table): void {
            $table->id();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email")->unique();
            $table->string("avatar")->nullable();
            $table->string("role")->default(Role::EMPLOYEE->value);
            $table->string("position");
            $table->string("employment_form");
            $table->date("employment_date");
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
