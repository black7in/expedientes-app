<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->foreignUuid('creado_por')->constrained('users');
            $table->string('tipo_documento', 50);
            $table->json('contexto_usado');
            $table->json('chunks_usados')->default('[]');
            $table->text('prompt_enviado')->nullable();
            $table->text('borrador_generado');
            $table->string('modelo_usado', 50)->nullable();
            $table->integer('tokens_usados')->nullable();
            $table->integer('tiempo_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generaciones');
    }
};
