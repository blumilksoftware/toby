<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("reports", function (Blueprint $table): void {
            $table->id();
            $table->string("name")->unique();
            $table->json("users")->nullable();
            $table->json("benefits")->nullable();
            $table->json("data")->nullable();
            $table->dateTime("committed_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("reports");
    }
};
