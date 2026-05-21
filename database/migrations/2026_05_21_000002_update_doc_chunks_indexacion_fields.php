<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doc_chunks', function (Blueprint $table) {
            // Sección semántica del documento: 'hechos', 'petitorio', 'clausulas', etc.
            $table->string('seccion', 50)->nullable()->after('tipo_doc');

            // Subtipo denormalizado para filtros rápidos
            $table->string('subtipo', 50)->nullable()->after('seccion');

            // Índice dentro de la sección si fue dividida por tamaño (>512 tokens)
            $table->integer('sub_chunk_index')->default(0)->after('chunk_index');

            // Modelo usado para generar el embedding — permite migraciones futuras
            $table->string('embedding_model', 50)->nullable()->after('metadata');
        });

        // Índice compuesto para filtrar por tipo + sección en búsqueda semántica
        DB::statement('CREATE INDEX idx_doc_chunks_tipo_seccion ON doc_chunks (tipo_doc, subtipo, seccion)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_doc_chunks_tipo_seccion');

        Schema::table('doc_chunks', function (Blueprint $table) {
            $table->dropColumn(['seccion', 'subtipo', 'sub_chunk_index', 'embedding_model']);
        });
    }
};
