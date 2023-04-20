<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("daily_summaries", function (Blueprint $table): void {
            $table->id();
            $table->date("day")->unique();
            $table->json("absences");
            $table->json("remotes");
            $table->json("birthdays");
            $table->string("channel_id")->nullable();
            $table->string("message_id")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("daily_summaries");
    }
};
