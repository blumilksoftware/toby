<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Eloquent\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("equipment_items", function (Blueprint $table): void {
            $table->id();
            $table->string("id_number")->unique();
            $table->string("name");
            $table->boolean("is_mobile")->default(false);
            $table->foreignIdFor(User::class, "assignee_id")->nullable()->constrained("users")->cascadeOnDelete();
            $table->date("assigned_at")->nullable();
            $table->json("labels")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("equipment_items");
    }
};
