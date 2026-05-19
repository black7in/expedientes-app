<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Clasificación de tipo de documento para el RAG (más granular que el enum tipo_documento)
            $table->string('tipo_doc', 50)->nullable()->after('texto_extraido');
            // Control de indexación vectorial
            $table->boolean('indexado')->default(false)->after('tipo_doc');
            $table->timestamp('indexado_at')->nullable()->after('indexado');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['tipo_doc', 'indexado', 'indexado_at']);
        });
    }
};
