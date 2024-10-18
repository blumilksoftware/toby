<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("holidays", function (Blueprint $table): void {
            $table->id();
            $table->foreignId("year_period_id")->constrained()->cascadeOnDelete();
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
