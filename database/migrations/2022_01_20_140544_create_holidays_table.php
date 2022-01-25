<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\YearPeriod;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("holidays", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(YearPeriod::class)->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->date("date")->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("holidays");
    }
};
