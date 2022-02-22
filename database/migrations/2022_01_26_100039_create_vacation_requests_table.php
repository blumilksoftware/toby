<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("vacation_requests", function (Blueprint $table): void {
            $table->id();
            $table->string("name");
            $table->foreignIdFor(User::class, "creator_id")->constrained("users")->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(YearPeriod::class)->constrained()->cascadeOnDelete();
            $table->string("type");
            $table->string("state")->nullable();
            $table->date("from");
            $table->date("to");
            $table->text("comment")->nullable();
            $table->boolean("flow_skipped")->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("vacation_requests");
    }
};
