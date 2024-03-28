<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;
use Toby\Models\VacationRequest;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("vacation_request_activities", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(VacationRequest::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string("from")->nullable();
            $table->string("to");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("vacation_request_activities");
    }
};
