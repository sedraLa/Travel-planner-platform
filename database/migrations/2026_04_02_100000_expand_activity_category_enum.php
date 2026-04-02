<?php

use App\Enums\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $values = collect(Category::values())
            ->map(fn (string $value) => "'{$value}'")
            ->implode(',');

        DB::statement("ALTER TABLE activities MODIFY category ENUM({$values}) NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE activities MODIFY category ENUM('culture','nature','shopping','sports','entertainment') NOT NULL");
    }
};
