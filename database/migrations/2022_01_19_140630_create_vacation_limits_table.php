<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;
use Toby\Models\YearPeriod;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("vacation_limits", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(YearPeriod::class)->constrained()->cascadeOnDelete();
            $table->integer("days")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("vacation_limits");
    }
};
