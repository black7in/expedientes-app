<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memoriales_moldes', function (Blueprint $table) {
            $table->string('doc_id')->primary();
            $table->string('content_hash')->unique();
            $table->longText('contenido');
            $table->text('resumen_caso');
            $table->string('tipo_memorial', 50);
            $table->string('tipo_accion', 80);
            $table->string('subtipo', 80)->nullable();
            $table->string('materia', 50);
            $table->string('instancia', 20);
            $table->string('jurisdiccion', 10)->default('BO');
            $table->string('formato', 20);
            $table->string('estilo', 50)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('origen', 30);
            $table->string('calidad', 20)->default('estandar');
            $table->boolean('activo')->default(true);
            $table->float('score_promedio')->nullable();
            $table->integer('usos_totales')->default(0);
            $table->float('tasa_aceptacion')->nullable();
            $table->timestampTz('fecha_indexacion')->useCurrent();
            $table->string('fecha_documento_original', 20)->nullable();
        });

        DB::statement('ALTER TABLE memoriales_moldes ADD COLUMN embedding vector(1024)');

        DB::statement('CREATE INDEX idx_moldes_filtros ON memoriales_moldes (tipo_memorial, tipo_accion, materia, instancia, activo)');
        DB::statement('CREATE INDEX idx_moldes_calidad ON memoriales_moldes (calidad, score_promedio DESC NULLS LAST)');
        DB::statement('CREATE INDEX idx_moldes_embedding ON memoriales_moldes USING ivfflat (embedding vector_cosine_ops) WITH (lists = 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('memoriales_moldes');
    }
};
