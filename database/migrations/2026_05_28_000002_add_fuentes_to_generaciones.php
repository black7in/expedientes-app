<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE generaciones ADD COLUMN IF NOT EXISTS fuentes JSONB NOT NULL DEFAULT '{}'");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE generaciones DROP COLUMN IF EXISTS fuentes');
    }
};
