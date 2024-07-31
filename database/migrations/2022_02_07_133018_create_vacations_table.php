<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("vacations", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(VacationRequest::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(YearPeriod::class)->constrained()->cascadeOnDelete();
            $table->string("event_id")->nullable();
            $table->date("date");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("vacations");
    }
};
