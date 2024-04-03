<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("resumes", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string("name")->nullable();
            $table->json("education");
            $table->json("languages");
            $table->json("technologies");
            $table->json("projects");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("resumes");
    }
};
