<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Artisan::call("toby:move-user-data-to-profile");

        Schema::table("users", function (Blueprint $table): void {
            $table->dropColumn("first_name");
            $table->dropColumn("last_name");
            $table->dropColumn("position");
            $table->dropColumn("employment_form");
            $table->dropColumn("employment_date");
        });
    }

    public function down(): void
    {
        Schema::table("users", function (Blueprint $table): void {
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->string("position")->nullable();
            $table->string("employment_form")->nullable();
            $table->date("employment_date")->nullable();
        });
    }
};
