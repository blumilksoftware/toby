<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("vacation_limits", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignId("year_period_id")->constrained()->cascadeOnDelete();
            $table->integer("days")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("vacation_limits");
    }
};
