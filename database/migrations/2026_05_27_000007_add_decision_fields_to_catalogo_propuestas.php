<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE catalogo_propuestas ADD COLUMN IF NOT EXISTS fecha_decision timestamptz');
        DB::statement('ALTER TABLE catalogo_propuestas ADD COLUMN IF NOT EXISTS decidido_por text');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE catalogo_propuestas DROP COLUMN IF EXISTS fecha_decision');
        DB::statement('ALTER TABLE catalogo_propuestas DROP COLUMN IF EXISTS decidido_por');
    }
};
