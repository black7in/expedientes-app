<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->foreignUuid('usuario_id')->constrained('users');
            $table->string('nombre_archivo');
            $table->enum('formato', ['pdf', 'docx']);
            $table->enum('tipo_documento', ['demanda', 'sentencia', 'memorial', 'contrato', 'notificacion', 'otro']);
            $table->enum('estado_extraccion', ['pendiente', 'procesado', 'error'])->default('pendiente');
            $table->json('texto_extraido')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
