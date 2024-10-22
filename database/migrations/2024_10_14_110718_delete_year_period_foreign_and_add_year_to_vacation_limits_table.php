<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Toby\Console\Commands\MigrateYearPeriodYearToVacationLimits;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("vacation_limits", function (Blueprint $table): void {
            $table->integer("year")->nullable();
        });

        Artisan::call(MigrateYearPeriodYearToVacationLimits::class);

        Schema::table("vacation_limits", function (Blueprint $table): void {
            $table->integer("year")->nullable(false)->change();
            $table->dropConstrainedForeignId("year_period_id");
        });
    }
};
