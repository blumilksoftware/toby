<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("profiles", function (Blueprint $table): void {
            $table->string("slack_id")->nullable();
            $table->date("birthday")->nullable();
        });
    }

    public function down(): void
    {
        Schema::table("profiles", function (Blueprint $table): void {
            $table->dropColumn("slack_id");
            $table->dropColumn("birthday");
        });
    }
};
