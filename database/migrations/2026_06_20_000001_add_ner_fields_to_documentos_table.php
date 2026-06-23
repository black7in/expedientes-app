<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->jsonb('entidades')->default(DB::raw("'[]'::jsonb"))->after('texto_extraido');
            $table->text('texto_anonimizado')->nullable()->after('entidades');
        });

        // Extiende el CHECK constraint del enum para agregar 'anonimizado'
        DB::statement("ALTER TABLE documentos DROP CONSTRAINT IF EXISTS documentos_estado_extraccion_check");
        DB::statement("ALTER TABLE documentos ADD CONSTRAINT documentos_estado_extraccion_check CHECK (estado_extraccion IN ('pendiente','procesado','anonimizado','error'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE documentos DROP CONSTRAINT IF EXISTS documentos_estado_extraccion_check");
        DB::statement("ALTER TABLE documentos ADD CONSTRAINT documentos_estado_extraccion_check CHECK (estado_extraccion IN ('pendiente','procesado','error'))");

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['entidades', 'texto_anonimizado']);
        });
    }
};
