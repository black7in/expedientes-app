<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE documentos DROP CONSTRAINT IF EXISTS documentos_estado_extraccion_check");
        DB::statement("ALTER TABLE documentos ADD CONSTRAINT documentos_estado_extraccion_check CHECK (estado_extraccion IN ('pendiente','procesado','anonimizado','error','pendiente_revision','confirmado'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE documentos DROP CONSTRAINT IF EXISTS documentos_estado_extraccion_check");
        DB::statement("ALTER TABLE documentos ADD CONSTRAINT documentos_estado_extraccion_check CHECK (estado_extraccion IN ('pendiente','procesado','anonimizado','error'))");
    }
};
