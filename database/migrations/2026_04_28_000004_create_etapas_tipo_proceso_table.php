<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapas_tipo_proceso', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tipo_proceso_id')->constrained('tipos_proceso')->onDelete('cascade');
            $table->integer('orden');
            $table->string('nombre');
            $table->enum('tipo', ['preliminar', 'escrito', 'audiencia', 'resolucion', 'recurso', 'ejecucion', 'otro']);
            $table->integer('plazo_dias')->nullable();
            $table->enum('tipo_computo', ['habil', 'corrido'])->nullable();
            $table->boolean('es_critica')->default(false);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas_tipo_proceso');
    }
};
