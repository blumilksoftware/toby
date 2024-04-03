<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    public function up(): void
    {
        DB::update(
            <<<DB
UPDATE model_has_permissions
SET model_type = 'Toby\Models\User'
WHERE model_type = 'Toby\Eloquent\Models\User';
DB
        );
    }

    public function down(): void
    {
        DB::update(
            <<<DB
UPDATE model_has_permissions
SET model_type = 'Toby\Eloquent\Models\User'
WHERE model_type = 'Toby\Models\User';
DB
        );
    }
};
