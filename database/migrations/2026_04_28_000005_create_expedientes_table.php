<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_expediente')->nullable();
            $table->foreignUuid('juzgado_id')->constrained('juzgados');
            $table->foreignUuid('tipo_proceso_id')->constrained('tipos_proceso');
            $table->foreignUuid('abogado_id')->constrained('users');
            $table->enum('estado', ['activo', 'suspendido', 'en_apelacion', 'archivado', 'concluido'])->default('activo');
            $table->timestamp('fecha_inicio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
