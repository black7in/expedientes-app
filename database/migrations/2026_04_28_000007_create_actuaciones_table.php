<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actuaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->foreignUuid('usuario_id')->constrained('users');
            $table->text('descripcion');
            $table->timestamp('fecha');
            $table->enum('tipo_actuacion', ['escrito', 'audiencia', 'resolucion', 'notificacion', 'recurso', 'otro']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actuaciones');
    }
};
