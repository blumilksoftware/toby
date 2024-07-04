<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("profiles", function (Blueprint $table): void {
            $table->dropColumn("last_medical_exam_date");
            $table->dropColumn("next_medical_exam_date");
            $table->dropColumn("last_ohs_training_date");
            $table->dropColumn("next_ohs_training_date");
            $table->dropColumn("employment_date");
        });
    }

    public function down(): void
    {
        Schema::table("profiles", function (Blueprint $table): void {
            $table->date("last_medical_exam_date")->nullable()->default(null);
            $table->date("next_medical_exam_date")->nullable()->default(null);
            $table->date("last_ohs_training_date")->nullable()->default(null);
            $table->date("next_ohs_training_date")->nullable()->default(null);
            $table->date("employment_date")->nullable();
        });
    }
};
