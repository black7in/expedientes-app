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
            // Deduplicación — SHA-256 del contenido del archivo
            $table->string('content_hash', 64)->nullable()->unique()->after('nombre_archivo');

            // Clasificación granular
            $table->string('subtipo', 50)->nullable()->after('tipo_documento');
            $table->enum('estructura', ['corrido', 'estructurado'])->nullable()->after('subtipo');

            // Detalle de error de extracción/parsing
            $table->text('error_mensaje')->nullable()->after('estado_extraccion');

            // Respuesta cruda del LLM parser (debug sin reprocesar)
            $table->json('raw_llm_response')->nullable()->after('texto_extraido');

            // Metadata extraída por el LLM: monto, partes, departamento, etc.
            $table->json('metadata')->default('{}')->after('raw_llm_response');

            // Versión del prompt usado — permite reindexar selectivamente
            $table->string('parser_version', 30)->nullable()->after('indexado_at');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn([
                'content_hash', 'subtipo', 'estructura', 'error_mensaje',
                'raw_llm_response', 'metadata', 'parser_version',
            ]);
        });
    }
};
