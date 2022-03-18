<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("vacation_requests", function (Blueprint $table): void {
            $table->json("event_ids")->nullable();
            $table->dropColumn("name");
        });
    }

    public function down(): void
    {
        Schema::table("vacation_requests", function (Blueprint $table): void {
            $table->dropColumn("event_ids");
            $table->string("name")->nullable();
        });
    }
};
