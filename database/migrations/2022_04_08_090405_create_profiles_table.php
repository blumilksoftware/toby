<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("profiles", function (Blueprint $table): void {
            $table->foreignIdFor(User::class)->primary();
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("position")->nullable();
            $table->string("employment_form")->nullable();
            $table->date("employment_date")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("profiles");
    }
};
