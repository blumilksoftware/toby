<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("overtime_requests", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
            $table->foreignIdFor(User::class, "creator_id")->constrained("users")->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignId("year_period_id")->constrained()->cascadeOnDelete();
            $table->string("state")->nullable();
            $table->string("settlement_type");
            $table->boolean("settled")->default(false);
            $table->dateTime("from");
            $table->dateTime("to");
            $table->integer("hours");
            $table->text("comment")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("overtime_requests");
    }
};
