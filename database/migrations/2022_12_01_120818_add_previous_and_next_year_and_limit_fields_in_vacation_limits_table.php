<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("vacation_limits", function (Blueprint $table): void {
            $table->integer("from_previous_year")->nullable();
            $table->integer("to_next_year")->nullable();
            $table->integer("limit")->nullable();
        });
    }

    public function down(): void
    {
        Schema::table("vacation_limits", function (Blueprint $table): void {
            $table->dropColumn("from_previous_year");
            $table->dropColumn("to_next_year");
            $table->dropColumn("limit");
        });
    }
};
