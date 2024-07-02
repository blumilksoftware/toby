<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("overtime_request_activities", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(OvertimeRequest::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string("from")->nullable();
            $table->string("to");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("overtime_request_activities");
    }
};
